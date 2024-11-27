@extends('layouts.template')
@section('title', 'Reporting')

@section('pageCSS')
@endsection
@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $date =  date('d-m-Y');
                $code =  $data['reporting_code'];
            }
            if($case == 'edit'){
                $id = $data['current']->reporting_id;
                $code = $data['current']->reporting_code;
                $date =  $data['current']->reporting_date;
                $title =  $data['current']->reporting_title;
                $case_name =  $data['current']->reporting_case;
                $table_name =  $data['current']->reporting_table_name;
                $reporting_dimension = isset($data['current']->reporting_dimension) ? $data['current']->reporting_dimension : [] ;
                $per_page_rows = $data['current']->reporting_rows_per_page;
                $sort_colum_name_1 = $data['current']->reporting_sort_colum_name_1;
                $sort_colum_name_2 = $data['current']->reporting_sort_colum_name_2;
                $sort_colum_1 = $data['current']->reporting_sort_colum_name_value_1;
                $sort_colum_2 = $data['current']->reporting_sort_colum_name_value_2;
            }
    @endphp

    <form id="reporting_form" class="kt-form" method="post" action="{{ action('Report\ReportController@store',isset($id)?$id:"") }}" enctype="multipart/form-data">
        @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    {{isset($code)?$code:""}}
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="row form-group-block">
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Title:</label>
                                    <input type="text" name="reporting_title" id="reporting_title" class="form-control erp-form-control-sm" value="{{isset($title)?$title:""}}"/>
                                </div>
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Case Name:</label>
                                    @if($case == 'edit')
                                        <input type="hidden" name="reporting_case" value="{{ $case_name }}"/>
                                        <input type="text" id="reporting_case" class="form-control erp-form-control-sm" value="{{ $case_name }}" disabled/>
                                    @else
                                        <input type="text" name="reporting_case" id="reporting_case" class="form-control erp-form-control-sm"/>
                                    @endif
                                </div>
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Select Table:</label>
                                    <div class="erp-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm" id="reporting_table_name" name="reporting_table_name">
                                            <option value="0">Select</option>
                                            @php $t_name = isset($table_name)?$table_name:""@endphp
                                            @foreach($data['table_list'] as $table_list)
                                                <option value="{{strtolower($table_list->table_name)}}" {{ ( strtolower($table_list->table_name) == $t_name )?"selected":"" }}>{{strtolower($table_list->table_name)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Date:</label>
                                    <div class="input-group date">
                                        <input type="text" name="reporting_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{$date}}" id="kt_datepicker_3" />
                                        <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    Select Table Flow
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Dimension:</label>
                                    <div class="erp-select2 report-select2">
                                        @php
                                            if($case == 'edit'){
                                                $allCols = \App\Models\ViewAllColumnData::where('table_name', strtoupper($table_name))->get();
                                            }
                                        @endphp
                                        <select class="form-control kt-select2 erp-form-control-sm" id="reporting_dimension_column_name" multiple name="reporting_dimension_column_name[]">
                                            @if($case == 'edit')
                                                @php $col = []; @endphp
                                                @foreach($data['current']->reporting_dimension as $dimension)
                                                    @php array_push($col,$dimension->reporting_dimension_column_name); @endphp
                                                @endforeach
                                                @foreach($col as $col_name)
                                                    <option value="{{$col_name}}" {{ (in_array($col_name, $col)) ? 'selected' : '' }}>{{$col_name}}</option>
                                                @endforeach
                                                @foreach($allCols as $col_name)
                                                    @if(!in_array(strtolower($col_name['column_name']), $col))
                                                        <option value="{{strtolower($col_name['column_name'])}}">{{strtolower($col_name['column_name'])}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Dimension Title:</label>
                                    <div class="erp-select2 report-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm" id="reporting_dimension_column_title" multiple name="reporting_dimension_column_title[]">
                                            @if($case == 'edit')
                                                @php $col = []; @endphp
                                                @foreach($data['current']->reporting_dimension as $dimension)
                                                    @php array_push($col,$dimension->reporting_dimension_column_title); @endphp
                                                @endforeach
                                                @foreach($col as $col_name)
                                                    <option value="{{$col_name}}" {{ (in_array($col_name, $col)) ? 'selected' : '' }}>{{$col_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Select Menu:</label>
                                    <div class="erp-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm" id="reporting_select_menu" name="reporting_select_menu">
                                            @foreach($data['reporting_menu'] as $reporting_menu)
                                                @php
                                                        $current_menu_dtl_id = isset($data['current']->menu_dtl_id)?$data['current']->menu_dtl_id:"";
                                                @endphp
                                                <option value="{{$reporting_menu->menu_dtl_id}}" {{$current_menu_dtl_id == $reporting_menu->menu_dtl_id ?"selected":""}}>{{$reporting_menu->menu_dtl_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet" id="kt_repeater_user_filter">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    Select User Filter
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div data-repeater-list="user_filter">
                                @if(isset($data['current']->user_filter) && count($data['current']->user_filter) != 0)
                                    @foreach($data['current']->user_filter as $user_filter)
                                        <div data-repeater-item class="user_filter_block">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <label class="erp-col-form-label">User Filter:</label>
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm reporting_user_filter_name" name="name">
                                                            @foreach($allCols as $col_name)
                                                                <option value="{{strtolower($col_name['column_name'])}}" {{ $user_filter['reporting_user_filter_field_name'] == strtolower($col_name['column_name'])?"selected":"" }}>{{strtolower($col_name['column_name'])}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label class="erp-col-form-label">User Filter Title:</label>
                                                    <input type="text" value="{{$user_filter['reporting_user_filter_title']}}" class="form-control erp-form-control-sm" id="reporting_user_filter_title" name="title">
                                                </div>
                                                <div class="col-lg-3">
                                                    <label class="erp-col-form-label">User Filter Type:</label>
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm reporting_user_filter_type" name="type">
                                                            <option value="0">Select</option>
                                                            <option value="boolean" {{ $user_filter['reporting_user_filter_field_type'] == 'boolean'?"selected":"" }}>Boolean</option>
                                                            <option value="date" {{ $user_filter['reporting_user_filter_field_type'] == 'date'?"selected":"" }}>Date</option>
                                                            <option value="number" {{ $user_filter['reporting_user_filter_field_type'] == 'number'?"selected":"" }}>Number</option>
                                                            <option value="varchar2" {{ $user_filter['reporting_user_filter_field_type'] == 'varchar2'?"selected":"" }}>Varchar</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 text-right">
                                                    <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger report-filter-del-btn">
                                                        <i class="la la-trash-o"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div data-repeater-item class="user_filter_block">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">User Filter:</label>
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm reporting_user_filter_name" name="name">
                                                        <option value="">Select</option>
                                                        @if($case == 'edit')
                                                            @foreach($allCols as $col_name)
                                                                <option value="{{strtolower($col_name['column_name'])}}">{{strtolower($col_name['column_name'])}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">User Filter Title:</label>
                                                <input type="text" class="form-control erp-form-control-sm" id="reporting_user_filter_title" name="title">
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">User Filter Type:</label>
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm reporting_user_filter_type" name="type">
                                                        <option value="0">Select</option>
                                                        <option value="boolean">Boolean</option>
                                                        <option value="date">Date</option>
                                                        <option value="number">Number</option>
                                                        <option value="varchar2">Varchar</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 text-right">
                                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger report-filter-del-btn">
                                                    <i class="la la-trash-o"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="kt-portlet__foot">
                            <div class="row">
                                <div class="col-lg-12 kt-align-right">
                                    <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
                                        Add User Filter
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    Table Setting
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Rows Per Page:</label>
                                    <div class="erp-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm" id="reporting_rows_per_page" name="reporting_rows_per_page">
                                            @php
                                                $per_page_rows_list = [10,20,25,50,100,250,500,1000,5000];
                                                $current_page_rows = isset($per_page_rows)?$per_page_rows:10;
                                            @endphp
                                            @foreach($per_page_rows_list as $per_page_row)
                                                <option value="{{$per_page_row}}" {{ $per_page_row == $current_page_rows?"selected":"" }}>{{$per_page_row}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Sort:</label>
                                    <div class="erp-select2 report-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm" id="reporting_sort_colum_name_1" name="reporting_sort_colum_name_1">
                                            @if($case == 'edit')
                                                <option value="">Select</option>
                                                @foreach($allCols as $col_name)
                                                    <option value="{{strtolower($col_name['column_name'])}}" {{ $sort_colum_name_1 == strtolower($col_name['column_name'])?"selected":"" }}>{{strtolower($col_name['column_name'])}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mt-1">
                                        @php
                                            $check_1 = isset($sort_colum_1)?$sort_colum_1:"";
                                        @endphp
                                        <label class="kt-radio kt-radio--success"> Ascending
                                            <input type="radio" class="" value="asc" name="reporting_sort_colum_name_value_1" {{$check_1 == "asc"?"checked":""}}>
                                            <span></span>
                                        </label>
                                        <label class="kt-radio kt-radio--success"> Descending
                                            <input type="radio" class="" value="desc" name="reporting_sort_colum_name_value_1" {{$check_1 == "desc"?"checked":""}}>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Secondary Sort:</label>
                                    <div class="erp-select2 report-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm" id="reporting_sort_colum_name_2" name="reporting_sort_colum_name_2">
                                            @if($case == 'edit')
                                                <option value="">Select</option>
                                                @foreach($allCols as $col_name)
                                                    <option value="{{strtolower($col_name['column_name'])}}" {{ $sort_colum_name_2 == strtolower($col_name['column_name'])?"selected":"" }}>{{strtolower($col_name['column_name'])}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mt-1">
                                        @php
                                            $check_2 = isset($sort_colum_2)?$sort_colum_2:"";
                                        @endphp
                                        <label class="kt-radio kt-radio--success"> Ascending
                                            <input type="radio" class="" value="asc" name="reporting_sort_colum_name_value_2"
                                                   {{$check_2 == "asc"?"checked":""}}>
                                            <span></span>
                                        </label>
                                        <label class="kt-radio kt-radio--success"> Descending
                                            <input type="radio" class="" value="desc" name="reporting_sort_colum_name_value_2"
                                                {{$check_2 == "desc"?"checked":""}}>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet" id="kt_repeater_metric">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    Metric
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div data-repeater-list="metric">
                                <div data-repeater-item class="metric_block">
                                    <div class="row form-group-block">
                                        <div class="col-lg-10">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <label class="erp-col-form-label">Metric:</label>
                                                    <div class="erp-select2 report-select2">
                                                        <select class="form-control erp-form-control-sm reporting_select_metric" name="reporting_select_metric">
                                                            {{-- @foreach($data['item'] as $tag)
                                                                 <option value="{{$tag->tags_id}}">{{$tag->tags_name}}</option>
                                                             @endforeach--}}
                                                            <option value="0">Select</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <div class="metric_data"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger report-filter-del-btn">
                                                <i class="la la-trash-o"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__foot">
                            <div class="row">
                                <div class="col-lg-12 kt-align-right">
                                    <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
                                        Add
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-12">
                            <div class="kt-portlet">
                                <div class="kt-portlet__head">
                                    <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">
                                            Add Filter
                                        </h3>
                                    </div>
                                </div>
                                <div class="kt-portlet__body" id="filter__body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label class="erp-col-form-label">Filter Name:</label>
                                            <div class="erp-select2 report-select2">
                                                @if(isset($data['current']->reporting_filter))
                                                    @foreach($data['current']->reporting_filter as $reporting_filter)
                                                        <input type="hidden" value="{{$reporting_filter->reporting_filter_id}}" name="reporting_filter_id" class="form-control erp-form-control-sm">
                                                        <input type="text" value="{{$reporting_filter->reporting_filter_name}}" name="reporting_filter_name" class="form-control erp-form-control-sm">
                                                    @endforeach
                                                @else
                                                    <input type="text" name="reporting_filter_name" class="form-control erp-form-control-sm">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div id="kt_repeater_1">
                                        <div data-repeater-list="outer_filterList">
                                            @if(isset($data['max']))
                                                @for($i=1; $data['max'] >= $i; $i++)
                                                    <div data-repeater-item class="outer-filter_block">
                                                            <div class="row">
                                                                <div class="col-lg-12" style="position: relative">
                                                                    <button data-repeater-delete="" type="button" class="btn btn-danger btn-sm report-user-filter-and-del-btn report-filter-and-del-btn">
                                                                        <i class="la la-trash-o"></i>  AND
                                                                    </button>
                                                                    <i class="la la-level-down user-report-and-down"></i>
                                                                </div>
                                                            </div>
                                                            <div class="inner-repeater">
                                                                <div data-repeater-list="inner_filterList">
                                                                    @if(isset($data['current']->reporting_filter))
                                                                        @foreach($data['current']->reporting_filter as $reporting_filter)
                                                                            @if(isset($reporting_filter->filter_dtl))
                                                                                @foreach($reporting_filter->filter_dtl as $reporting_filter_dtl)
                                                                                    @if($reporting_filter_dtl['reporting_filter_sr_no'] == $i)
                                                                                        <div data-repeater-item class="col-lg-12 filter_block">
                                                                                        <div class="row form-group-block">
                                                                                            <div class="col-lg-10">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-3">
                                                                                                        <label class="erp-col-form-label">Filter Name:</label>
                                                                                                        <div class="erp-select2 report-select2">
                                                                                                            <select class="form-control erp-form-control-sm report_fields_name" name="report_fields_name">
                                                                                                                @foreach($allCols as $col_name)
                                                                                                                    <option value="{{strtolower($col_name['column_name'])}}" {{ $reporting_filter_dtl['reporting_filter_column_name'] == strtolower($col_name['column_name'])?"selected":"" }}>{{strtolower($col_name['column_name'])}}</option>
                                                                                                                @endforeach
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-lg-9">
                                                                                                        <div class="row">
                                                                                                            <div class="col-lg-4" id="report_filter_types">
                                                                                                                <label class="erp-col-form-label">Condition:</label>
                                                                                                                @php
                                                                                                                    $datatype = \App\Models\TblSoftReportingFilterDtl::where('reporting_filter_id', $reporting_filter['reporting_filter_id'])
                                                                                                                                          ->where('reporting_filter_column_name', $reporting_filter_dtl['reporting_filter_column_name'])
                                                                                                                                          ->first();
                                                                                                                    $types = \App\Models\TblSoftFilterType::where('filter_type_data_type_name', $datatype->reporting_filter_field_type)->get();
                                                                                                                @endphp
                                                                                                                {{--{{$datatype->reporting_filter_field_type ." = ". $reporting_filter_dtl['reporting_filter_condition']}}--}}
                                                                                                                <div class="erp-select2 report-select2">
                                                                                                                    <select class="form-control erp-form-control-sm report_condition" name="report_condition">
                                                                                                                        @foreach($types as $type)
                                                                                                                            <option value="{{$type->filter_type_value}}" {{ ($type->filter_type_value == $reporting_filter_dtl['reporting_filter_condition'])?"selected":"" }}>{{$type->filter_type_title}}</option>
                                                                                                                        @endforeach
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <style>
                                                                                                                #number_between,
                                                                                                                #date_between,
                                                                                                                #fields_values{
                                                                                                                    display: none;
                                                                                                                }
                                                                                                                .erp-row{
                                                                                                                    display: flex;
                                                                                                                    flex-wrap: wrap;
                                                                                                                    margin-right: -10px;
                                                                                                                    margin-left: -10px;
                                                                                                                }
                                                                                                            </style>
                                                                                                            <input type="hidden" id="report_value_column_type_name" name="report_value_column_type_name" value="{{$datatype->reporting_filter_field_type}}"/>
                                                                                                            <div class="col-lg-8" id="report_filter_block">
                                                                                                                @if($datatype->reporting_filter_field_type == 'varchar2' || $datatype->reporting_user_filter_field_type == 'number')
                                                                                                                    <div class="row" id="fields_values"  style="display: block">
                                                                                                                        <div class="col-lg-12">
                                                                                                                            <label class="erp-col-form-label">Value:</label>
                                                                                                                            <input type="text" value="{{ $reporting_filter_dtl['reporting_filter_value'] }}" name="report_value" class="report_value form-control erp-form-control-sm">
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                @endif
                                                                                                                @if($datatype->reporting_filter_field_type == 'number' && $reporting_filter_dtl['reporting_filter_condition'] == 'between')
                                                                                                                    <div class="row" id="number_between"  style="display: block">
                                                                                                                        <div class="col-lg-12">
                                                                                                                            <div class="erp-row">
                                                                                                                                <div class="col-lg-6">
                                                                                                                                    <label class="erp-col-form-label">From:</label>
                                                                                                                                    <input type="text" value="{{ $reporting_filter_dtl['reporting_filter_value'] }}" name="report_value" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                                </div>
                                                                                                                                <div class="col-lg-6">
                                                                                                                                    <label class="erp-col-form-label">To:</label>
                                                                                                                                    <input type="text" value="{{ $reporting_filter_dtl['reporting_filter_value_2'] }}" name="report_value_to" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                                </div>
                                                                                                                            </div>

                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                @endif
                                                                                                                @if($datatype->reporting_filter_field_type == 'date')
                                                                                                                    <div class="row" id="date_between" style="display: block">
                                                                                                                        <div class="col-lg-12">
                                                                                                                            <label class="erp-col-form-label">Select Date Range:</label>
                                                                                                                            <div class="erp-selectDateRange">
                                                                                                                                <div class="input-daterange input-group kt_datepicker_5">
                                                                                                                                    <input type="text" value="{{ $reporting_filter_dtl['reporting_filter_value'] }}" class="form-control erp-form-control-sm" name="report_value" />
                                                                                                                                    <div class="input-group-append">
                                                                                                                                        <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                                                    </div>
                                                                                                                                    <input type="text" value="{{ $reporting_filter_dtl['reporting_filter_value_2'] }}" class="form-control erp-form-control-sm" name="report_value_to" />
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                @endif
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-2 text-right">
                                                                                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger report-user-filter-del-btn">
                                                                                                    <i class="la la-minus-circle"></i>
                                                                                                </a>
                                                                                                <a href="javascript:;" class="btn btn-bold btn-sm btn-label-brand report-user-filter-or-btn" disabled readonly >
                                                                                                    OR
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    @endif
                                                                                @endforeach
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                                <div class="row ">
                                                                    <div class="col-lg-9"></div>
                                                                    <div class="col-lg-3  text-right">
                                                                        <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand report-user-sec-filter-or-btn report-user-filter-or-btn">
                                                                            OR
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                @endfor
                                            @else
                                                <div data-repeater-item class="outer-filter_block">
                                                    <div class="row">
                                                        <div class="col-lg-12" style="position: relative">
                                                            <button data-repeater-delete="" type="button" class="btn btn-danger btn-sm report-user-filter-and-del-btn report-filter-and-del-btn">
                                                                <i class="la la-trash-o"></i>  AND
                                                            </button>
                                                            <i class="la la-level-down user-report-and-down"></i>
                                                        </div>
                                                    </div>
                                                    <div class="inner-repeater">
                                                        <div data-repeater-list="inner_filterList">
                                                            <div data-repeater-item class="col-lg-12 filter_block">
                                                                <div class="row form-group-block">
                                                                    <div class="col-lg-10">
                                                                        <div class="row">
                                                                            <div class="col-lg-3">
                                                                                <label class="erp-col-form-label">Filter Name:</label>
                                                                                <div class="erp-select2 report-select2">
                                                                                    <select class="form-control erp-form-control-sm report_fields_name" name="report_fields_name">
                                                                                        <option value="">Select</option>
                                                                                        @if($case == 'edit')
                                                                                            @foreach($allCols as $col_name)
                                                                                                <option value="{{strtolower($col_name['column_name'])}}">{{strtolower($col_name['column_name'])}}</option>
                                                                                            @endforeach
                                                                                        @endif
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-9">
                                                                                <div class="row">
                                                                                    <div class="col-lg-4" id="report_filter_types">
                                                                                        <label class="erp-col-form-label">Condition:</label>
                                                                                        <div class="erp-select2 report-select2">
                                                                                            <select class="form-control erp-form-control-sm report_condition" name="report_condition">
                                                                                                <option value="">Select</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <style>
                                                                                        #number_between,
                                                                                        #date_between,
                                                                                        #fields_values{
                                                                                            display: none;
                                                                                        }
                                                                                        .erp-row{
                                                                                            display: flex;
                                                                                            flex-wrap: wrap;
                                                                                            margin-right: -10px;
                                                                                            margin-left: -10px;
                                                                                        }
                                                                                    </style>
                                                                                    <input type="hidden" id="report_value_column_type_name" name="report_value_column_type_name"/>
                                                                                    <div class="col-lg-8" id="report_filter_block">
                                                                                        <div class="row" id="fields_values">
                                                                                            <div class="col-lg-12">
                                                                                                <label class="erp-col-form-label">Value:</label>
                                                                                                <input type="text" disabled name="report_value" class="report_value form-control erp-form-control-sm">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row" id="number_between">
                                                                                            <div class="col-lg-12">
                                                                                                <div class="erp-row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <label class="erp-col-form-label">From:</label>
                                                                                                        <input type="text" disabled name="report_value" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <label class="erp-col-form-label">To:</label>
                                                                                                        <input type="text" disabled name="report_value_to" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                    </div>
                                                                                                </div>

                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row" id="date_between">
                                                                                            <div class="col-lg-12">
                                                                                                <label class="erp-col-form-label">Select Date Range:</label>
                                                                                                <div class="erp-selectDateRange">
                                                                                                    <div class="input-daterange input-group kt_datepicker_5">
                                                                                                        <input type="text" disabled class="form-control erp-form-control-sm" name="report_value" />
                                                                                                        <div class="input-group-append">
                                                                                                            <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                        </div>
                                                                                                        <input type="text" disabled class="form-control erp-form-control-sm" name="report_value_to" />
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 text-right">
                                                                        <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger report-user-filter-del-btn">
                                                                            <i class="la la-minus-circle"></i>
                                                                        </a>
                                                                        <a href="javascript:;" class="btn btn-bold btn-sm btn-label-brand report-user-filter-or-btn" disabled readonly >
                                                                            OR
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row ">
                                                            <div class="col-lg-9"></div>
                                                            <div class="col-lg-3  text-right">
                                                                <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand report-user-sec-filter-or-btn report-user-filter-or-btn">
                                                                    OR
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button data-repeater-create type="button" class="btn btn-brand btn-sm">AND</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('report/table_style')
                </div>
            </div>
        </div>
    </form>
    <!--end::Form-->
@endsection
@section('pageJS')
    @if($case == 'edit')
        <script>
            var cloumnsData = '';
            var cloumnsList = '';
            var column_type_name = '';
        </script>
        @foreach($allCols as $col_name)
            <script>
                cloumnsList += '<option value="{{strtolower($col_name['column_name'])}}">{{strtolower($col_name['column_name'])}}</option>';
            </script>
        @endforeach
    @else
        <script>
            var cloumnsData = '';
            var cloumnsList = '';
            var column_type_name = '';
        </script>
    @endif
@endsection

@section('customJS')
<script src="{{ asset('js/pages/js/reporting.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/js/report-req.js') }}" type="text/javascript"></script>
<script src="/assets/js/pages/crud/forms/widgets/form-repeater.js" type="text/javascript"></script>
<script>
    $("#reporting_dimension_column_name").on("select2:select", function (e) {
        var id = e.params.data.id;
        var option = $(e.target).children('[value='+id+']');
        option.detach();
        $(e.target).append(option).change();
    });
    $("#reporting_dimension_column_title").on("select2:select", function (e) {
        var id = e.params.data.id;
        var option = $(e.target).children('[value='+id+']');
        option.detach();
        $(e.target).append(option).change();
    });
</script>
@endsection

