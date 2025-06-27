@extends('layouts.pattern')
@section('title', 'Listing Studio')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : '';
        if ($case == 'new') {
            $date = date('d-m-Y');
            $code = $data['listing_studio_code'];
            $select_menu = '';
            $menu_dtl_id = -1;
            $listing_studio_view_type = 'business';
        }
        if ($case == 'edit') {
            $id = $data['current']->listing_studio_id;
            $code = $data['current']->listing_studio_code;
            $date = $data['current']->listing_studio_date;
            $title = $data['current']->listing_studio_title;
            $case_name = $data['current']->listing_studio_case;
            $table_name = $data['current']->listing_studio_table_name;
            $select_menu = $data['current']->listing_studio_type;
            $parent_menu = $data['current']->listing_studio_parent_menu;
            $menu_dtl_id = $data['current']->menu_dtl_id;
            $per_page_rows = $data['current']->listing_studio_rows_per_page;
            $sort_colum_name_1 = $data['current']->listing_studio_sort_colum_name_1;
            $sort_colum_name_2 = $data['current']->listing_studio_sort_colum_name_2;
            $sort_colum_1 = $data['current']->listing_studio_sort_colum_name_value_1;
            $sort_colum_2 = $data['current']->listing_studio_sort_colum_name_value_2;
            $listing_studio_group_by = $data['current']->listing_studio_group_by;
            $listing_studio_view_type = $data['current']->listing_studio_view_type;
            $reporting_dimension = isset($data['current']->listing_studio_dimension)
                ? $data['current']->listing_studio_dimension
                : [];
            $join_data = isset($data['current']->join_table) ? $data['current']->join_table : [];
            if (count($join_data) != 0) {
                $join_table_name = $data['current']->join_table[0]->listing_studio_join_table_name;
            }
        }
    @endphp
    @permission($data['permission'])
        <!--begin::Form-->
        <form id="listing_studio_form" class="erp_form_validation kt-form" method="post"
            action="{{ action('Development\ListingStudioController@store', isset($id) ? $id : '') }}">
            @csrf
            <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                <div class="kt-portlet kt-portlet--mobile">
                    <div class="kt-portlet__head kt-portlet__head--lg">
                        @include('elements.page_header', ['page_data' => $data['page_data']])
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        {{ isset($code) ? $code : '' }}
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <div class="row form-group-block">
                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">Title:</label>
                                        <input type="text" name="listing_studio_title"
                                            class="form-control erp-form-control-sm" value="{{ isset($title) ? $title : '' }}" />
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">Case Name:</label>
                                        @if ($case == 'edit')
                                            <input type="hidden" name="listing_studio_case" value="{{ $case_name }}" />
                                            <input type="text" class="form-control erp-form-control-sm"
                                                value="{{ $case_name }}" disabled />
                                        @else
                                            <input type="text" name="listing_studio_case"
                                                class="form-control erp-form-control-sm" />
                                        @endif
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">Select Table:</label>
                                        <div class="erp-select2 form-group">
                                            <select
                                                class="form-control kt-select2 erp-form-control-sm listing_studio_table_name"
                                                name="listing_studio_table_name">
                                                <option value="0">Select</option>
                                                @php $t_name = isset($table_name)?$table_name:""@endphp
                                                @foreach ($data['table_list'] as $table_list)
                                                    <option value="{{ strtolower($table_list->table_name) }}"
                                                        {{ strtolower($table_list->table_name) == $t_name ? 'selected' : '' }}>
                                                        {{ strtolower($table_list->table_name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">Date:</label>
                                        <div class="input-group date">
                                            <input type="text" name="listing_studio_date"
                                                class="form-control erp-form-control-sm c-date-p" readonly
                                                value="{{ $date }}" id="kt_datepicker_3" />
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
                                        <label class="erp-col-form-label">Listing View Type:</label>
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm listing_studio_view_type"
                                                name="listing_studio_view_type">
                                                <option value="business"
                                                    {{ $listing_studio_view_type == 'business' ? 'selected' : '' }}>Business
                                                </option>
                                                <option value="branch"
                                                    {{ $listing_studio_view_type == 'branch' ? 'selected' : '' }}>Branch</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">Listing Type:</label>
                                        <div class="erp-select2">
                                            <select
                                                class="form-control kt-select2 erp-form-control-sm listing_studio_select_menu"
                                                name="listing_studio_select_menu">
                                                <option value="help" {{ $select_menu == 'help' ? 'selected' : '' }}>Help</option>
                                                <option value="main_listing"
                                                    {{ $select_menu == 'main_listing' ? 'selected' : '' }}>Main Listing</option>
                                            </select>
                                        </div>
                                    </div>
                                    @if ($case == 'edit' && $select_menu == 'main_listing')
                                        <div class="col-lg-3" id="listing_studio_select_menu_dtl_id" style="display: block">
                                            <label class="erp-col-form-label">Select Menu:</label>
                                            <div class="erp-select2">
                                                <select
                                                    class="form-control kt-select2 erp-form-control-sm listing_studio_select_menu_dtl_id"
                                                    name="listing_studio_select_menu_dtl_id">
                                                    @foreach ($data['MenuDtl'] as $MenuDtl)
                                                        <option value="{{ $MenuDtl->menu_dtl_id }}"
                                                            {{ $MenuDtl->menu_dtl_id == $menu_dtl_id ? 'selected' : '' }}>
                                                            {{ $MenuDtl->menu_dtl_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3" id="listing_studio_parent_menu" style="display: block">
                                            <label class="erp-col-form-label">Parent Menu:</label>
                                            <div class="erp-select2">
                                                <select
                                                    class="form-control kt-select2 erp-form-control-sm listing_studio_parent_menu"
                                                    name="listing_studio_parent_menu">
                                                    <option value="0">Select</option>
                                                    <option value="accounts" {{ $parent_menu == 'accounts' ? 'selected' : '' }}>
                                                        Accounts</option>
                                                    <option value="stock" {{ $parent_menu == 'stock' ? 'selected' : '' }}>Stock
                                                    </option>
                                                    <option value="day" {{ $parent_menu == 'day' ? 'selected' : '' }}>Day
                                                    </option>
                                                    <option value="barcode-labels"
                                                        {{ $parent_menu == 'barcode-labels' ? 'selected' : '' }}>Barcode Labels
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-lg-3" id="listing_studio_select_menu_dtl_id" style="display: none">
                                            <label class="erp-col-form-label">Select Menu:</label>
                                            <div class="erp-select2">
                                                <select
                                                    class="form-control kt-select2 erp-form-control-sm listing_studio_select_menu_dtl_id"
                                                    name="listing_studio_select_menu_dtl_id">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3" id="listing_studio_parent_menu" style="display: none">
                                            <label class="erp-col-form-label">Parent Menu:</label>
                                            <div class="erp-select2">
                                                <select
                                                    class="form-control kt-select2 erp-form-control-sm listing_studio_parent_menu"
                                                    name="listing_studio_parent_menu">
                                                </select>
                                            </div>
                                        </div>
                                    @endif
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
                                        <div class="form-group erp-select2 report-select2 column_name">
                                            @php
                                                if ($case == 'edit') {
                                                    $allCols = \App\Models\ViewAllColumnData::where(
                                                        'table_name',
                                                        strtoupper($table_name),
                                                    )->get();
                                                }
                                            @endphp
                                            <select
                                                class="form-control kt-select2 erp-form-control-sm listing_studio_dimension_column_name"
                                                multiple name="listing_studio_dimension_column_name[]">
                                                @if ($case == 'edit')
                                                    @php $col = []; @endphp
                                                    @foreach ($data['current']->listing_studio_dimension as $dimension)
                                                        @php array_push($col,$dimension->listing_studio_dimension_column_name); @endphp
                                                    @endforeach
                                                    @foreach ($col as $col_name)
                                                        <option value="{{ $col_name }}"
                                                            {{ in_array($col_name, $col) ? 'selected' : '' }}>
                                                            {{ $col_name }}</option>
                                                    @endforeach
                                                    @foreach ($allCols as $col_name)
                                                        @if (!in_array(strtolower($col_name['column_name']), $col))
                                                            <option value="{{ strtolower($col_name['column_name']) }}">
                                                                {{ strtolower($col_name['column_name']) }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">Dimension Title:</label>
                                        <div class="erp-select2 report-select2 column_title">
                                            <select
                                                class="form-control erp-form-control-sm listing_studio_dimension_column_title"
                                                multiple name="listing_studio_dimension_column_title[]">
                                                @if ($case == 'edit')
                                                    @php $col = []; @endphp
                                                    @foreach ($data['current']->listing_studio_dimension as $dimension)
                                                        @php array_push($col,$dimension->listing_studio_dimension_column_title); @endphp
                                                    @endforeach
                                                    @foreach ($col as $col_name)
                                                        <option value="{{ $col_name }}"
                                                            {{ in_array($col_name, $col) ? 'selected' : '' }}>
                                                            {{ $col_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mt-1">
                                            <label class="erp-col-form-label">Group By:</label>
                                            <div class="mt-1">
                                                <label class="kt-checkbox kt-checkbox--success">
                                                    @php $group_by = isset($listing_studio_group_by)?$listing_studio_group_by:""; @endphp
                                                    <input type="checkbox" class="" value="desc"
                                                        name="listing_studio_group_by" {{ $group_by == 1 ? 'checked' : '' }}>
                                                    <span></span>
                                                </label>
                                            </div>
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
                                    @if (isset($data['current']->listing_studio_user_filter) && count($data['current']->listing_studio_user_filter) != 0)
                                        @foreach ($data['current']->listing_studio_user_filter as $user_filter)
                                            <div data-repeater-item class="user_filter_block">
                                                <div class="row">
                                                    <div class="col-lg-9">
                                                        <div class="row">
                                                            <div class="col-lg-3">
                                                                <label class="erp-col-form-label">User Filter:</label>
                                                                <div class="erp-select2">
                                                                    <select
                                                                        class="form-control erp-form-control-sm listing_studio_user_filter_name"
                                                                        name="listing_studio_user_filter_name">
                                                                        <option value="0">Select</option>
                                                                        @foreach ($allCols as $col_name)
                                                                            <option
                                                                                value="{{ strtolower($col_name['column_name']) }}"
                                                                                {{ $user_filter['listing_studio_user_filter_name'] == strtolower($col_name['column_name']) ? 'selected' : '' }}>
                                                                                {{ strtolower($col_name['column_name']) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <label class="erp-col-form-label">User Filter Title:</label>
                                                                <input type="text"
                                                                    value="{{ $user_filter['listing_studio_user_filter_title'] }}"
                                                                    class="form-control erp-form-control-sm listing_studio_user_filter_title"
                                                                    name="listing_studio_user_filter_title">
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <label class="erp-col-form-label">User Filter Type:</label>
                                                                <div class="erp-select2">
                                                                    <select
                                                                        class="form-control erp-form-control-sm listing_studio_user_filter_type"
                                                                        name="listing_studio_user_filter_type">
                                                                        <option value="0">Select</option>
                                                                        <option value="boolean"
                                                                            {{ $user_filter['listing_studio_user_filter_type'] == 'boolean' ? 'selected' : '' }}>
                                                                            Boolean</option>
                                                                        <option value="date"
                                                                            {{ $user_filter['listing_studio_user_filter_type'] == 'date' ? 'selected' : '' }}>
                                                                            Date</option>
                                                                        <option value="number"
                                                                            {{ $user_filter['listing_studio_user_filter_type'] == 'number' ? 'selected' : '' }}>
                                                                            Number</option>
                                                                        <option value="varchar2"
                                                                            {{ $user_filter['listing_studio_user_filter_type'] == 'varchar2' ? 'selected' : '' }}>
                                                                            Varchar</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <label class="erp-col-form-label">Filter Case:</label>
                                                                <div class="erp-select2">
                                                                    <select
                                                                        class="form-control erp-form-control-sm listing_studio_user_case_name"
                                                                        name="listing_studio_user_case_name">
                                                                        <option value="0">Select</option>
                                                                        @foreach ($data['filter_case_list'] as $case_list)
                                                                            <option
                                                                                value="{{ $case_list->reporting_filter_case_id }}"
                                                                                {{ $user_filter['listing_studio_user_case_name'] == strtolower($case_list->reporting_filter_case_id) ? 'selected' : '' }}>
                                                                                {{ $case_list->reporting_filter_case_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 text-right">
                                                        <a href="javascript:;" data-repeater-delete=""
                                                            class="btn btn-sm btn-label-danger report-filter-del-btn">
                                                            <i class="la la-trash-o"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="user_filter_block">
                                            <div class="row">
                                                <div class="col-lg-9">
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <label class="erp-col-form-label">User Filter:</label>
                                                            <div class="erp-select2">
                                                                <select
                                                                    class="form-control erp-form-control-sm listing_studio_user_filter_name"
                                                                    name="listing_studio_user_filter_name">
                                                                    <option value="0">Select</option>
                                                                    @if ($case == 'edit')
                                                                        @foreach ($allCols as $col_name)
                                                                            <option
                                                                                value="{{ strtolower($col_name['column_name']) }}">
                                                                                {{ strtolower($col_name['column_name']) }}
                                                                            </option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <label class="erp-col-form-label">User Filter Title:</label>
                                                            <input type="text"
                                                                class="form-control erp-form-control-sm listing_studio_user_filter_title"
                                                                name="listing_studio_user_filter_title">
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <label class="erp-col-form-label">User Filter Type:</label>
                                                            <div class="erp-select2">
                                                                <select
                                                                    class="form-control erp-form-control-sm listing_studio_user_filter_type"
                                                                    name="listing_studio_user_filter_type">
                                                                    <option value="0">Select</option>
                                                                    <option value="boolean">Boolean</option>
                                                                    <option value="date">Date</option>
                                                                    <option value="number">Number</option>
                                                                    <option value="varchar2">Varchar</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <label class="erp-col-form-label">Filter Case:</label>
                                                            <div class="erp-select2">
                                                                <select
                                                                    class="form-control erp-form-control-sm listing_studio_user_case_name"
                                                                    name="listing_studio_user_case_name">
                                                                    <option value="0">Select</option>
                                                                    @foreach ($data['filter_case_list'] as $case_list)
                                                                        <option
                                                                            value="{{ $case_list->reporting_filter_case_id }}">
                                                                            {{ $case_list->reporting_filter_case_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 text-right">
                                                    <a href="javascript:;" data-repeater-delete=""
                                                        class="btn btn-sm btn-label-danger report-filter-del-btn">
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
                                        <a href="javascript:;" data-repeater-create=""
                                            class="btn btn-bold btn-sm btn-label-brand">
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
                                            <select
                                                class="form-control kt-select2 erp-form-control-sm listing_studio_rows_per_page"
                                                name="listing_studio_rows_per_page">
                                                @php
                                                    $per_page_rows_list = [
                                                        10,
                                                        20,
                                                        25,
                                                        50,
                                                        100,
                                                        200,
                                                        250,
                                                        500,
                                                        1000,
                                                        5000,
                                                    ];
                                                    $current_page_rows = isset($per_page_rows) ? $per_page_rows : 50;
                                                @endphp
                                                @foreach ($per_page_rows_list as $per_page_row)
                                                    <option value="{{ $per_page_row }}"
                                                        {{ $per_page_row == $current_page_rows ? 'selected' : '' }}>
                                                        {{ $per_page_row }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">Sort:</label>
                                        <div class="erp-select2 report-select2">
                                            <select
                                                class="form-control kt-select2 erp-form-control-sm listing_studio_sort_colum_name_1"
                                                name="listing_studio_sort_colum_name_1">
                                                @if ($case == 'edit')
                                                    <option value="">Select</option>
                                                    @foreach ($allCols as $col_name)
                                                        <option value="{{ strtolower($col_name['column_name']) }}"
                                                            {{ $sort_colum_name_1 == strtolower($col_name['column_name']) ? 'selected' : '' }}>
                                                            {{ strtolower($col_name['column_name']) }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="mt-1">
                                            @php
                                                $check_1 = isset($sort_colum_1) ? $sort_colum_1 : '';
                                            @endphp
                                            @if ($case == 'edit')
                                                <label class="kt-radio kt-radio--success"> Ascending
                                                    <input type="radio" class="" value="asc"
                                                        name="listing_studio_sort_colum_name_value_1"
                                                        {{ $check_1 == 'asc' ? 'checked' : '' }}>
                                                    <span></span>
                                                </label>
                                            @else
                                                <label class="kt-radio kt-radio--success"> Ascending
                                                    <input type="radio" class="" value="asc"
                                                        name="listing_studio_sort_colum_name_value_1" checked>
                                                    <span></span>
                                                </label>
                                            @endif
                                            <label class="kt-radio kt-radio--success"> Descending
                                                <input type="radio" class="" value="desc"
                                                    name="listing_studio_sort_colum_name_value_1"
                                                    {{ $check_1 == 'desc' ? 'checked' : '' }}>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">Secondary Sort:</label>
                                        <div class="erp-select2 report-select2">
                                            <select
                                                class="form-control kt-select2 erp-form-control-sm listing_studio_sort_colum_name_2"
                                                name="listing_studio_sort_colum_name_2">
                                                @if ($case == 'edit')
                                                    <option value="">Select</option>
                                                    @foreach ($allCols as $col_name)
                                                        <option value="{{ strtolower($col_name['column_name']) }}"
                                                            {{ $sort_colum_name_2 == strtolower($col_name['column_name']) ? 'selected' : '' }}>
                                                            {{ strtolower($col_name['column_name']) }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="mt-1">
                                            @php
                                                $check_2 = isset($sort_colum_2) ? $sort_colum_2 : '';
                                            @endphp
                                            @if ($case == 'edit')
                                                <label class="kt-radio kt-radio--success"> Ascending
                                                    <input type="radio" class="" value="asc"
                                                        name="listing_studio_sort_colum_name_value_2"
                                                        {{ $check_2 == 'asc' ? 'checked' : '' }}>
                                                    <span></span>
                                                </label>
                                            @else
                                                <label class="kt-radio kt-radio--success"> Ascending
                                                    <input type="radio" class="" value="asc"
                                                        name="listing_studio_sort_colum_name_value_2" checked>
                                                    <span></span>
                                                </label>
                                            @endif
                                            <label class="kt-radio kt-radio--success"> Descending
                                                <input type="radio" class="" value="desc"
                                                    name="listing_studio_sort_colum_name_value_2"
                                                    {{ $check_2 == 'desc' ? 'checked' : '' }}>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        Default Filter
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <div id="kt_repeater_1">
                                    <div data-repeater-list="outer_filterList">
                                        @if (isset($data['max']))
                                            @for ($i = 1; $data['max'] >= $i; $i++)
                                                <div data-repeater-item class="outer-filter_block">
                                                    <div class="row">
                                                        <div class="col-lg-12" style="position: relative">
                                                            <button data-repeater-delete="" type="button"
                                                                class="btn btn-danger btn-sm report-user-filter-and-del-btn report-filter-and-del-btn">
                                                                <i class="la la-trash-o"></i> AND
                                                            </button>
                                                            <i class="la la-level-down user-report-and-down"></i>
                                                        </div>
                                                    </div>
                                                    <div class="inner-repeater">
                                                        <div data-repeater-list="inner_filterList">
                                                            @if (isset($data['current']->listing_studio_default_filter))
                                                                @foreach ($data['current']->listing_studio_default_filter as $default_filter)
                                                                    @if ($default_filter['listing_studio_default_filter_sr'] == $i)
                                                                        <div data-repeater-item class="col-lg-12 filter_block">
                                                                            <div class="row form-group-block">
                                                                                <div class="col-lg-10">
                                                                                    <div class="row">
                                                                                        <div class="col-lg-3">
                                                                                            <label
                                                                                                class="erp-col-form-label">Filter
                                                                                                Name:</label>
                                                                                            <div
                                                                                                class="erp-select2 report-select2">
                                                                                                <select
                                                                                                    class="form-control erp-form-control-sm report_fields_name"
                                                                                                    name="listing_studio_default_filter_name">
                                                                                                    <option value="0">
                                                                                                        Select</option>
                                                                                                    @foreach ($allCols as $col_name)
                                                                                                        <option
                                                                                                            value="{{ strtolower($col_name['column_name']) }}"
                                                                                                            {{ $default_filter['listing_studio_default_filter_name'] == strtolower($col_name['column_name']) ? 'selected' : '' }}>
                                                                                                            {{ strtolower($col_name['column_name']) }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-lg-9">
                                                                                            <div class="row">
                                                                                                <div class="col-lg-4"
                                                                                                    id="report_filter_types">
                                                                                                    <label
                                                                                                        class="erp-col-form-label">Condition:</label>
                                                                                                    @php
                                                                                                        $datatype = \App\Models\TblSoftListingStudioDefaultFilter::where(
                                                                                                            'listing_studio_default_filter_id',
                                                                                                            $default_filter[
                                                                                                                'listing_studio_default_filter_id'
                                                                                                            ],
                                                                                                        )
                                                                                                            ->where(
                                                                                                                'listing_studio_default_filter_name',
                                                                                                                $default_filter[
                                                                                                                    'listing_studio_default_filter_name'
                                                                                                                ],
                                                                                                            )
                                                                                                            ->first();
                                                                                                        $types = \App\Models\TblSoftFilterType::where(
                                                                                                            'filter_type_data_type_name',
                                                                                                            $datatype->listing_studio_default_filter_field_type,
                                                                                                        )
                                                                                                            ->where(
                                                                                                                'filter_type_entry_status',
                                                                                                                1,
                                                                                                            )
                                                                                                            ->get();
                                                                                                    @endphp
                                                                                                    {{-- {{$datatype->reporting_filter_field_type ." = ". $default_filter['reporting_filter_condition']}} --}}
                                                                                                    <div
                                                                                                        class="erp-select2 report-select2">
                                                                                                        <select
                                                                                                            class="form-control erp-form-control-sm report_condition"
                                                                                                            name="listing_studio_default_filter_condition">
                                                                                                            @foreach ($types as $type)
                                                                                                                <option
                                                                                                                    value="{{ $type->filter_type_value }}"
                                                                                                                    {{ $type->filter_type_value == $default_filter['listing_studio_default_filter_condition'] ? 'selected' : '' }}>
                                                                                                                    {{ $type->filter_type_title }}
                                                                                                                </option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <style>
                                                                                                    #number_between,
                                                                                                    #date_between,
                                                                                                    #fields_values {
                                                                                                        display: none;
                                                                                                    }

                                                                                                    .erp-row {
                                                                                                        display: flex;
                                                                                                        flex-wrap: wrap;
                                                                                                        margin-right: -10px;
                                                                                                        margin-left: -10px;
                                                                                                    }
                                                                                                </style>
                                                                                                <input type="hidden"
                                                                                                    id="report_value_column_type_name"
                                                                                                    name="listing_studio_default_filter_field_type"
                                                                                                    value="{{ $datatype->listing_studio_default_filter_field_type }}" />
                                                                                                <div class="col-lg-8"
                                                                                                    id="report_filter_block">
                                                                                                    @if ($datatype->listing_studio_default_filter_field_type == 'boolean')
                                                                                                        <div class="row"
                                                                                                            id="fields_values"
                                                                                                            style="display: none">
                                                                                                            <div
                                                                                                                class="col-lg-12">
                                                                                                                <label
                                                                                                                    class="erp-col-form-label">Value:</label>
                                                                                                                <input disabled
                                                                                                                    type="text"
                                                                                                                    name="listing_studio_default_filter_value"
                                                                                                                    class="report_value form-control erp-form-control-sm">
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="row"
                                                                                                            id="number_between"
                                                                                                            style="display: none">
                                                                                                            <div
                                                                                                                class="col-lg-12">
                                                                                                                <div
                                                                                                                    class="erp-row">
                                                                                                                    <div
                                                                                                                        class="col-lg-6">
                                                                                                                        <label
                                                                                                                            class="erp-col-form-label">From:</label>
                                                                                                                        <input
                                                                                                                            disabled
                                                                                                                            type="text"
                                                                                                                            name="listing_studio_default_filter_value"
                                                                                                                            class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                    </div>
                                                                                                                    <div
                                                                                                                        class="col-lg-6">
                                                                                                                        <label
                                                                                                                            class="erp-col-form-label">To:</label>
                                                                                                                        <input
                                                                                                                            disabled
                                                                                                                            type="text"
                                                                                                                            name="listing_studio_default_filter_value_2"
                                                                                                                            class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="row"
                                                                                                            id="date_between"
                                                                                                            style="display: none">
                                                                                                            <div
                                                                                                                class="col-lg-12">
                                                                                                                <label
                                                                                                                    class="erp-col-form-label">Select
                                                                                                                    Date
                                                                                                                    Range:</label>
                                                                                                                <div
                                                                                                                    class="erp-selectDateRange">
                                                                                                                    <div
                                                                                                                        class="input-daterange input-group kt_datepicker_5">
                                                                                                                        <input
                                                                                                                            disabled
                                                                                                                            type="text"
                                                                                                                            class="form-control erp-form-control-sm"
                                                                                                                            name="listing_studio_default_filter_value" />
                                                                                                                        <div
                                                                                                                            class="input-group-append">
                                                                                                                            <span
                                                                                                                                class="input-group-text erp-form-control-sm">To</span>
                                                                                                                        </div>
                                                                                                                        <input
                                                                                                                            disabled
                                                                                                                            type="text"
                                                                                                                            class="form-control erp-form-control-sm"
                                                                                                                            name="listing_studio_default_filter_value_2" />
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                    @if ($datatype->listing_studio_default_filter_field_type == 'varchar2')
                                                                                                        @if (
                                                                                                            $default_filter['listing_studio_default_filter_condition'] == 'null' ||
                                                                                                                $default_filter['listing_studio_default_filter_condition'] == 'not null')
                                                                                                            <div class="row"
                                                                                                                id="fields_values"
                                                                                                                style="display: none">
                                                                                                                <div
                                                                                                                    class="col-lg-12">
                                                                                                                    <label
                                                                                                                        class="erp-col-form-label">Value:</label>
                                                                                                                    <input
                                                                                                                        disabled
                                                                                                                        type="text"
                                                                                                                        name="listing_studio_default_filter_value"
                                                                                                                        class="report_value form-control erp-form-control-sm">
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row"
                                                                                                                id="number_between"
                                                                                                                style="display: none">
                                                                                                                <div
                                                                                                                    class="col-lg-12">
                                                                                                                    <div
                                                                                                                        class="erp-row">
                                                                                                                        <div
                                                                                                                            class="col-lg-6">
                                                                                                                            <label
                                                                                                                                class="erp-col-form-label">From:</label>
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                name="listing_studio_default_filter_value"
                                                                                                                                class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                        </div>
                                                                                                                        <div
                                                                                                                            class="col-lg-6">
                                                                                                                            <label
                                                                                                                                class="erp-col-form-label">To:</label>
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                name="listing_studio_default_filter_value_2"
                                                                                                                                class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row"
                                                                                                                id="date_between"
                                                                                                                style="display: none">
                                                                                                                <div
                                                                                                                    class="col-lg-12">
                                                                                                                    <label
                                                                                                                        class="erp-col-form-label">Select
                                                                                                                        Date
                                                                                                                        Range:</label>
                                                                                                                    <div
                                                                                                                        class="erp-selectDateRange">
                                                                                                                        <div
                                                                                                                            class="input-daterange input-group kt_datepicker_5">
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                class="form-control erp-form-control-sm"
                                                                                                                                name="listing_studio_default_filter_value" />
                                                                                                                            <div
                                                                                                                                class="input-group-append">
                                                                                                                                <span
                                                                                                                                    class="input-group-text erp-form-control-sm">To</span>
                                                                                                                            </div>
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                class="form-control erp-form-control-sm"
                                                                                                                                name="listing_studio_default_filter_value_2" />
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        @else
                                                                                                            <div class="row"
                                                                                                                id="fields_values"
                                                                                                                style="display: block">
                                                                                                                <div
                                                                                                                    class="col-lg-12">
                                                                                                                    <label
                                                                                                                        class="erp-col-form-label">Value:</label>
                                                                                                                    <input
                                                                                                                        type="text"
                                                                                                                        value="{{ $default_filter['listing_studio_default_filter_value'] }}"
                                                                                                                        name="listing_studio_default_filter_value"
                                                                                                                        class="report_value form-control erp-form-control-sm">
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row"
                                                                                                                id="number_between"
                                                                                                                style="display: none">
                                                                                                                <div
                                                                                                                    class="col-lg-12">
                                                                                                                    <div
                                                                                                                        class="erp-row">
                                                                                                                        <div
                                                                                                                            class="col-lg-6">
                                                                                                                            <label
                                                                                                                                class="erp-col-form-label">From:</label>
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                name="listing_studio_default_filter_value"
                                                                                                                                class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                        </div>
                                                                                                                        <div
                                                                                                                            class="col-lg-6">
                                                                                                                            <label
                                                                                                                                class="erp-col-form-label">To:</label>
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                name="listing_studio_default_filter_value_2"
                                                                                                                                class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row"
                                                                                                                id="date_between"
                                                                                                                style="display: none">
                                                                                                                <div
                                                                                                                    class="col-lg-12">
                                                                                                                    <label
                                                                                                                        class="erp-col-form-label">Select
                                                                                                                        Date
                                                                                                                        Range:</label>
                                                                                                                    <div
                                                                                                                        class="erp-selectDateRange">
                                                                                                                        <div
                                                                                                                            class="input-daterange input-group kt_datepicker_5">
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                class="form-control erp-form-control-sm"
                                                                                                                                name="listing_studio_default_filter_value" />
                                                                                                                            <div
                                                                                                                                class="input-group-append">
                                                                                                                                <span
                                                                                                                                    class="input-group-text erp-form-control-sm">To</span>
                                                                                                                            </div>
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                class="form-control erp-form-control-sm"
                                                                                                                                name="listing_studio_default_filter_value_2" />
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        @endif
                                                                                                    @endif
                                                                                                    @if ($datatype->listing_studio_default_filter_field_type == 'number')
                                                                                                        @if ($default_filter['listing_studio_default_filter_condition'] == 'between')
                                                                                                            <div class="row"
                                                                                                                id="number_between"
                                                                                                                style="display: block">
                                                                                                                <div
                                                                                                                    class="col-lg-12">
                                                                                                                    <div
                                                                                                                        class="erp-row">
                                                                                                                        <div
                                                                                                                            class="col-lg-6">
                                                                                                                            <label
                                                                                                                                class="erp-col-form-label">From:</label>
                                                                                                                            <input
                                                                                                                                type="text"
                                                                                                                                value="{{ $default_filter['listing_studio_default_filter_value'] }}"
                                                                                                                                name="listing_studio_default_filter_value"
                                                                                                                                class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                        </div>
                                                                                                                        <div
                                                                                                                            class="col-lg-6">
                                                                                                                            <label
                                                                                                                                class="erp-col-form-label">To:</label>
                                                                                                                            <input
                                                                                                                                type="text"
                                                                                                                                value="{{ $default_filter['listing_studio_default_filter_value_2'] }}"
                                                                                                                                name="listing_studio_default_filter_value_2"
                                                                                                                                class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row"
                                                                                                                id="fields_values"
                                                                                                                style="display: none">
                                                                                                                <div
                                                                                                                    class="col-lg-12">
                                                                                                                    <label
                                                                                                                        class="erp-col-form-label">Value:</label>
                                                                                                                    <input
                                                                                                                        disabled
                                                                                                                        type="text"
                                                                                                                        name="listing_studio_default_filter_value"
                                                                                                                        class="report_value form-control erp-form-control-sm">
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row"
                                                                                                                id="date_between"
                                                                                                                style="display: none">
                                                                                                                <div
                                                                                                                    class="col-lg-12">
                                                                                                                    <label
                                                                                                                        class="erp-col-form-label">Select
                                                                                                                        Date
                                                                                                                        Range:</label>
                                                                                                                    <div
                                                                                                                        class="erp-selectDateRange">
                                                                                                                        <div
                                                                                                                            class="input-daterange input-group kt_datepicker_5">
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                class="form-control erp-form-control-sm"
                                                                                                                                name="listing_studio_default_filter_value" />
                                                                                                                            <div
                                                                                                                                class="input-group-append">
                                                                                                                                <span
                                                                                                                                    class="input-group-text erp-form-control-sm">To</span>
                                                                                                                            </div>
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                class="form-control erp-form-control-sm"
                                                                                                                                name="listing_studio_default_filter_value_2" />
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        @else
                                                                                                            <div class="row"
                                                                                                                id="fields_values"
                                                                                                                style="display: block">
                                                                                                                <div
                                                                                                                    class="col-lg-12">
                                                                                                                    <label
                                                                                                                        class="erp-col-form-label">Value:</label>
                                                                                                                    <input
                                                                                                                        type="text"
                                                                                                                        value="{{ $default_filter['listing_studio_default_filter_value'] }}"
                                                                                                                        name="listing_studio_default_filter_value"
                                                                                                                        class="report_value form-control erp-form-control-sm">
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row"
                                                                                                                id="number_between"
                                                                                                                style="display: none">
                                                                                                                <div
                                                                                                                    class="col-lg-12">
                                                                                                                    <div
                                                                                                                        class="erp-row">
                                                                                                                        <div
                                                                                                                            class="col-lg-6">
                                                                                                                            <label
                                                                                                                                class="erp-col-form-label">From:</label>
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                name="listing_studio_default_filter_value"
                                                                                                                                class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                        </div>
                                                                                                                        <div
                                                                                                                            class="col-lg-6">
                                                                                                                            <label
                                                                                                                                class="erp-col-form-label">To:</label>
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                name="listing_studio_default_filter_value_2"
                                                                                                                                class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row"
                                                                                                                id="date_between"
                                                                                                                style="display: none">
                                                                                                                <div
                                                                                                                    class="col-lg-12">
                                                                                                                    <label
                                                                                                                        class="erp-col-form-label">Select
                                                                                                                        Date
                                                                                                                        Range:</label>
                                                                                                                    <div
                                                                                                                        class="erp-selectDateRange">
                                                                                                                        <div
                                                                                                                            class="input-daterange input-group kt_datepicker_5">
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                class="form-control erp-form-control-sm"
                                                                                                                                name="listing_studio_default_filter_value" />
                                                                                                                            <div
                                                                                                                                class="input-group-append">
                                                                                                                                <span
                                                                                                                                    class="input-group-text erp-form-control-sm">To</span>
                                                                                                                            </div>
                                                                                                                            <input
                                                                                                                                disabled
                                                                                                                                type="text"
                                                                                                                                class="form-control erp-form-control-sm"
                                                                                                                                name="listing_studio_default_filter_value_2" />
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        @endif
                                                                                                    @endif
                                                                                                    @if ($datatype->listing_studio_default_filter_field_type == 'date')
                                                                                                        <div class="row"
                                                                                                            id="date_between"
                                                                                                            style="display: block">
                                                                                                            <div
                                                                                                                class="col-lg-12">
                                                                                                                <label
                                                                                                                    class="erp-col-form-label">Select
                                                                                                                    Date
                                                                                                                    Range:</label>
                                                                                                                <div
                                                                                                                    class="erp-selectDateRange">
                                                                                                                    <div
                                                                                                                        class="input-daterange input-group kt_datepicker_5">
                                                                                                                        <input
                                                                                                                            type="text"
                                                                                                                            value="{{ $default_filter['listing_studio_default_filter_value'] }}"
                                                                                                                            class="form-control erp-form-control-sm"
                                                                                                                            name="listing_studio_default_filter_value" />
                                                                                                                        <div
                                                                                                                            class="input-group-append">
                                                                                                                            <span
                                                                                                                                class="input-group-text erp-form-control-sm">To</span>
                                                                                                                        </div>
                                                                                                                        <input
                                                                                                                            type="text"
                                                                                                                            value="{{ $default_filter['listing_studio_default_filter_value_2'] }}"
                                                                                                                            class="form-control erp-form-control-sm"
                                                                                                                            name="listing_studio_default_filter_value_2" />
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="row"
                                                                                                            id="fields_values"
                                                                                                            style="display: none">
                                                                                                            <div
                                                                                                                class="col-lg-12">
                                                                                                                <label
                                                                                                                    class="erp-col-form-label">Value:</label>
                                                                                                                <input disabled
                                                                                                                    type="text"
                                                                                                                    name="listing_studio_default_filter_value"
                                                                                                                    class="report_value form-control erp-form-control-sm">
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="row"
                                                                                                            id="number_between"
                                                                                                            style="display: none">
                                                                                                            <div
                                                                                                                class="col-lg-12">
                                                                                                                <div
                                                                                                                    class="erp-row">
                                                                                                                    <div
                                                                                                                        class="col-lg-6">
                                                                                                                        <label
                                                                                                                            class="erp-col-form-label">From:</label>
                                                                                                                        <input
                                                                                                                            disabled
                                                                                                                            type="text"
                                                                                                                            name="listing_studio_default_filter_value"
                                                                                                                            class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                    </div>
                                                                                                                    <div
                                                                                                                        class="col-lg-6">
                                                                                                                        <label
                                                                                                                            class="erp-col-form-label">To:</label>
                                                                                                                        <input
                                                                                                                            disabled
                                                                                                                            type="text"
                                                                                                                            name="listing_studio_default_filter_value_2"
                                                                                                                            class="form-control erp-form-control-sm text-left validNumber">
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
                                                                                    <a href="javascript:;"
                                                                                        data-repeater-delete=""
                                                                                        class="btn btn-sm btn-label-danger report-user-filter-del-btn">
                                                                                        <i class="la la-minus-circle"></i>
                                                                                    </a>
                                                                                    <a href="javascript:;"
                                                                                        class="btn btn-bold btn-sm btn-label-brand report-user-filter-or-btn"
                                                                                        disabled readonly>
                                                                                        OR
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                        <div class="row ">
                                                            <div class="col-lg-9"></div>
                                                            <div class="col-lg-3  text-right">
                                                                <a href="javascript:;" data-repeater-create=""
                                                                    class="btn btn-bold btn-sm btn-label-brand report-user-sec-filter-or-btn report-user-filter-or-btn">
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
                                                        <button data-repeater-delete="" type="button"
                                                            class="btn btn-danger btn-sm report-user-filter-and-del-btn report-filter-and-del-btn">
                                                            <i class="la la-trash-o"></i> AND
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
                                                                            <label class="erp-col-form-label">Filter
                                                                                Name:</label>
                                                                            <div class="erp-select2 report-select2">
                                                                                <select
                                                                                    class="form-control erp-form-control-sm report_fields_name"
                                                                                    name="listing_studio_default_filter_name">
                                                                                    <option value="">Select</option>
                                                                                    @if ($case == 'edit')
                                                                                        @foreach ($allCols as $col_name)
                                                                                            <option
                                                                                                value="{{ strtolower($col_name['column_name']) }}">
                                                                                                {{ strtolower($col_name['column_name']) }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    @endif
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-9">
                                                                            <div class="row">
                                                                                <div class="col-lg-4"
                                                                                    id="report_filter_types">
                                                                                    <label
                                                                                        class="erp-col-form-label">Condition:</label>
                                                                                    <div class="erp-select2 report-select2">
                                                                                        <select
                                                                                            class="form-control erp-form-control-sm report_condition"
                                                                                            name="listing_studio_default_filter_condition">
                                                                                            <option value="">Select
                                                                                            </option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <style>
                                                                                    #number_between,
                                                                                    #date_between,
                                                                                    #fields_values {
                                                                                        display: none;
                                                                                    }

                                                                                    .erp-row {
                                                                                        display: flex;
                                                                                        flex-wrap: wrap;
                                                                                        margin-right: -10px;
                                                                                        margin-left: -10px;
                                                                                    }
                                                                                </style>
                                                                                <input type="hidden"
                                                                                    id="report_value_column_type_name"
                                                                                    name="listing_studio_default_filter_field_type" />
                                                                                <div class="col-lg-8"
                                                                                    id="report_filter_block">
                                                                                    <div class="row" id="fields_values">
                                                                                        <div class="col-lg-12">
                                                                                            <label
                                                                                                class="erp-col-form-label">Value:</label>
                                                                                            <input type="text" disabled
                                                                                                name="listing_studio_default_filter_value"
                                                                                                class="report_value form-control erp-form-control-sm">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row" id="number_between">
                                                                                        <div class="col-lg-12">
                                                                                            <div class="erp-row">
                                                                                                <div class="col-lg-6">
                                                                                                    <label
                                                                                                        class="erp-col-form-label">From:</label>
                                                                                                    <input type="text"
                                                                                                        disabled
                                                                                                        name="listing_studio_default_filter_value"
                                                                                                        class="form-control erp-form-control-sm text-left validNumber">
                                                                                                </div>
                                                                                                <div class="col-lg-6">
                                                                                                    <label
                                                                                                        class="erp-col-form-label">To:</label>
                                                                                                    <input type="text"
                                                                                                        disabled
                                                                                                        name="listing_studio_default_filter_value_2"
                                                                                                        class="form-control erp-form-control-sm text-left validNumber">
                                                                                                </div>
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row" id="date_between">
                                                                                        <div class="col-lg-12">
                                                                                            <label
                                                                                                class="erp-col-form-label">Select
                                                                                                Date Range:</label>
                                                                                            <div class="erp-selectDateRange">
                                                                                                <div
                                                                                                    class="input-daterange input-group kt_datepicker_5">
                                                                                                    <input type="text"
                                                                                                        disabled
                                                                                                        class="form-control erp-form-control-sm"
                                                                                                        name="listing_studio_default_filter_value" />
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text erp-form-control-sm">To</span>
                                                                                                    </div>
                                                                                                    <input type="text"
                                                                                                        disabled
                                                                                                        class="form-control erp-form-control-sm"
                                                                                                        name="listing_studio_default_filter_value_2" />
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
                                                                    <a href="javascript:;" data-repeater-delete=""
                                                                        class="btn btn-sm btn-label-danger report-user-filter-del-btn">
                                                                        <i class="la la-minus-circle"></i>
                                                                    </a>
                                                                    <a href="javascript:;"
                                                                        class="btn btn-bold btn-sm btn-label-brand report-user-filter-or-btn"
                                                                        disabled readonly>
                                                                        OR
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row ">
                                                        <div class="col-lg-9"></div>
                                                        <div class="col-lg-3  text-right">
                                                            <a href="javascript:;" data-repeater-create=""
                                                                class="btn btn-bold btn-sm btn-label-brand report-user-sec-filter-or-btn report-user-filter-or-btn">
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
                                            <button data-repeater-create type="button"
                                                class="btn btn-brand btn-sm">AND</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet" id="kt_repeater_metric">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        Select Metric
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <div data-repeater-list="metric">
                                    @if (isset($data['current']->listing_studio_metric) && count($data['current']->listing_studio_metric) != 0)
                                        @foreach ($data['current']->listing_studio_metric as $metric_aggre)
                                            <div data-repeater-item class="metric_block">
                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <label class="erp-col-form-label">Metric Field:</label>
                                                        <div class="erp-select2">
                                                            <select
                                                                class="form-control erp-form-control-sm listing_studio_metric_column_name"
                                                                name="listing_studio_metric_column_name">
                                                                <option value="0">Select</option>
                                                                @if ($case == 'edit')
                                                                    @foreach ($allCols as $col_name)
                                                                        <option
                                                                            value="{{ strtolower($col_name['column_name']) }}"
                                                                            {{ $metric_aggre['listing_studio_metric_column_name'] == strtolower($col_name['column_name']) ? 'selected' : '' }}>
                                                                            {{ strtolower($col_name['column_name']) }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="erp-col-form-label">Metric Field Title:</label>
                                                        <input type="text"
                                                            value="{{ $metric_aggre['listing_studio_metric_column_title'] }}"
                                                            class="form-control erp-form-control-sm listing_studio_metric_column_title"
                                                            name="listing_studio_metric_column_title">
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="erp-col-form-label">Aggregation:</label>
                                                        <div class="erp-select2">
                                                            <select
                                                                class="form-control erp-form-control-sm listing_studio_metric_aggregation"
                                                                name="listing_studio_metric_aggregation">
                                                                <option value="0">Select</option>
                                                                <option value="sum"
                                                                    {{ $metric_aggre['listing_studio_metric_aggregation'] == 'sum' ? 'selected' : '' }}>
                                                                    Sum</option>
                                                                <option value="min"
                                                                    {{ $metric_aggre['listing_studio_metric_aggregation'] == 'min' ? 'selected' : '' }}>
                                                                    Min</option>
                                                                <option value="max"
                                                                    {{ $metric_aggre['listing_studio_metric_aggregation'] == 'max' ? 'selected' : '' }}>
                                                                    Max</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 text-right">
                                                        <a href="javascript:;" data-repeater-delete=""
                                                            class="btn btn-sm btn-label-danger report-filter-del-btn">
                                                            <i class="la la-trash-o"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="metric_block">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <label class="erp-col-form-label">Metric Field:</label>
                                                    <div class="erp-select2">
                                                        <select
                                                            class="form-control erp-form-control-sm listing_studio_metric_column_name"
                                                            name="listing_studio_metric_column_name">
                                                            <option value="0">Select</option>
                                                            @if ($case == 'edit')
                                                                @foreach ($allCols as $col_name)
                                                                    <option
                                                                        value="{{ strtolower($col_name['column_name']) }}">
                                                                        {{ strtolower($col_name['column_name']) }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label class="erp-col-form-label">Metric Field Title:</label>
                                                    <input type="text"
                                                        class="form-control erp-form-control-sm listing_studio_metric_column_title"
                                                        name="listing_studio_metric_column_title">
                                                </div>
                                                <div class="col-lg-3">
                                                    <label class="erp-col-form-label">Aggregation:</label>
                                                    <div class="erp-select2">
                                                        <select
                                                            class="form-control erp-form-control-sm listing_studio_metric_aggregation"
                                                            name="listing_studio_metric_aggregation">
                                                            <option value="0">Select</option>
                                                            <option value="sum">Sum</option>
                                                            <option value="min">Min</option>
                                                            <option value="max">Max</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 text-right">
                                                    <a href="javascript:;" data-repeater-delete=""
                                                        class="btn btn-sm btn-label-danger report-filter-del-btn">
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
                                        <a href="javascript:;" data-repeater-create=""
                                            class="btn btn-bold btn-sm btn-label-brand">
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
                                        Join
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">Select Table:</label>
                                        <div class="erp-select2 form-group">
                                            <select
                                                class="form-control kt-select2 erp-form-control-sm listing_studio_join_name"
                                                name="listing_studio_join_name">
                                                <option value="0">Select</option>
                                                @php $t_join_name = isset($join_table_name)?$join_table_name:""@endphp
                                                @foreach ($data['table_list'] as $table_list)
                                                    <option value="{{ strtolower($table_list->table_name) }}"
                                                        {{ strtolower($table_list->table_name) == $t_join_name ? 'selected' : '' }}>
                                                        {{ strtolower($table_list->table_name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">Dimension:</label>
                                        <div class="form-group erp-select2 report-select2 join_table_column_name">
                                            @php
                                                if ($case == 'edit') {
                                                    $j_allCols = \App\Models\ViewAllColumnData::where(
                                                        'table_name',
                                                        strtoupper($t_join_name),
                                                    )->get();
                                                }
                                            @endphp
                                            <select
                                                class="form-control erp-form-control-sm listing_studio_join_table_column_name"
                                                multiple name="listing_studio_join_table_column_name[]">
                                                @if ($case == 'edit')
                                                    @php $j_col = []; @endphp
                                                    @foreach ($join_data as $dimension)
                                                        @php array_push($j_col,$dimension->listing_studio_join_table_column_name); @endphp
                                                    @endforeach
                                                    @foreach ($j_col as $col_name)
                                                        <option value="{{ $col_name }}"
                                                            {{ in_array($col_name, $j_col) ? 'selected' : '' }}>
                                                            {{ $col_name }}</option>
                                                    @endforeach
                                                    @foreach ($j_allCols as $col_name)
                                                        @if (!in_array(strtolower($col_name['column_name']), $col))
                                                            <option value="{{ strtolower($col_name['column_name']) }}">
                                                                {{ strtolower($col_name['column_name']) }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">Dimension Title:</label>
                                        <div class="erp-select2 report-select2 join_table_column_title">
                                            <select
                                                class="form-control erp-form-control-sm listing_studio_join_table_column_title"
                                                multiple name="listing_studio_join_table_column_title[]">
                                                @if ($case == 'edit')
                                                    @php $jt_col = []; @endphp
                                                    @foreach ($join_data as $dimension)
                                                        @php array_push($jt_col,$dimension->listing_studio_join_table_column_title); @endphp
                                                    @endforeach
                                                    @foreach ($jt_col as $col_name)
                                                        <option value="{{ $col_name }}"
                                                            {{ in_array($col_name, $jt_col) ? 'selected' : '' }}>
                                                            {{ $col_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if ($case == 'edit')
                            <div class="kt-portlet" id="listing_studio_query">
                                <div class="kt-portlet__head">
                                    <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">
                                            Query
                                        </h3>
                                    </div>
                                </div>
                                <div class="kt-portlet__body">
                                    {{ $data['query'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')

@endsection

@section('customJS')
    @if ($case == 'edit')
        <script>
            var cloumnsData = '';
            var cloumnsList = '';
            var column_type_name = '';
        </script>
        @foreach ($allCols as $col_name)
            <script>
                cloumnsList +=
                    '<option value="{{ strtolower($col_name['column_name']) }}">{{ strtolower($col_name['column_name']) }}</option>';
            </script>
        @endforeach
    @else
        <script>
            var cloumnsData = '';
            var cloumnsList = '';
            var column_type_name = '';
        </script>
    @endif
    <script src="{{ asset('js/pages/js/listing-studio.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/listing-studio-data-repeater.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/listing-studio-req-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js') }}" type="text/javascript"></script>

    <script>
        $('.kt-select2, .kt-select2_validate').select2({
            placeholder: "Select"
        });
        $('.listing_studio_dimension_column_title, .listing_studio_dimension_column_title_validate').select2({
            placeholder: "Dimension Titles",
            tags: true
        });
        $(".listing_studio_dimension_column_name").on("select2:select", function(e) {
            var id = e.params.data.id;
            var option = $(e.target).children("[value='" + id + "']");
            option.detach();
            $(e.target).append(option).change();
        });
        $(".listing_studio_dimension_column_title").on("select2:select", function(e) {
            var id = e.params.data.id;
            var option = $(e.target).children("[value='" + id + "']");
            option.detach();
            $(e.target).append(option).change();
        });

        $('.listing_studio_join_table_column_name, .listing_studio_join_table_column_title').select2({
            placeholder: "Titles",
            tags: true
        });
        $(".listing_studio_join_table_column_name,.listing_studio_join_table_column_title").on("select2:select", function(
        e) {
            var id = e.params.data.id;
            var option = $(e.target).children("[value='" + id + "']");
            option.detach();
            $(e.target).append(option).change();
        });
        $(".join_table_column_name>.select2>.selection>.select2-selection>ul.select2-selection__rendered").sortable({
            containment: 'parent',
            update: function(event, ui) {

            }
        });
        $(".join_table_column_title>.select2>.selection>.select2-selection>ul.select2-selection__rendered").sortable({
            containment: 'parent',
            update: function(event, ui) {

            }
        });
        $(".column_title>.select2>.selection>.select2-selection>ul.select2-selection__rendered").sortable({
            containment: 'parent',
            update: function(event, ui) {

            }
        });
        $(".column_name>.select2>.selection>.select2-selection>ul.select2-selection__rendered").sortable({
            containment: 'parent',
            update: function(event, ui) {

            }
        });

        function update_ColumnName_OptionsField() {
            var selectBlockCN = 'column_name';
            var ulCN = $("." + selectBlockCN + ">.select2>.selection>.select2-selection>ul.select2-selection__rendered");
            var lengthCN = ulCN.find('li').length;
            var allLiTitlesCN = [];
            ulCN.find('li').each(function(index) {
                if (index !== (lengthCN - 1)) {
                    allLiTitlesCN.push($(this).attr('title'));
                }
            })
            var selectElementsCN = $("." + selectBlockCN + ">select");
            selectElementsCN.find('option:selected').remove();
            var optionsCN = '';
            for (var i = 0; i < allLiTitlesCN.length; i++) {
                optionsCN += '<option value="' + allLiTitlesCN[i] + '" data-select2-id="' + i + '" selected>' +
                    allLiTitlesCN[i] + '<option>';
            }
            console.log(optionsCN);
            selectElementsCN.append(optionsCN);
            selectElementsCN.find('option').filter(function() {
                return !this.value || $.trim(this.value).length == 0 || $.trim(this.text).length == 0;
            }).remove();
        }
    </script>
@endsection
