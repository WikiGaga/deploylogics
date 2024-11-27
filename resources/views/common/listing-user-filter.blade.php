<style>
    .modal-footer{justify-content: space-between !important;}
    .modal-footer input[type='checkbox']{vertical-align:middle;}
</style>
<form action="GET" name="filter-form">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filters</h5>
        <button type="button" class="close" onclick="closeUserFilterModal()" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        <div id="kt_user_listing_repeater">
            <div data-repeater-list="outer_filterList">
                @if(isset($data['UserFilter']) &&  count($data['UserFilter']) != 0)
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
                                        @if(isset($data['queryArray']) && count($data['queryArray']) != 0)
                                            @foreach($data['queryArray'] as $default_filter)
                                                @if($default_filter->sr_no == $i)
                                                    <div data-repeater-item class="col-lg-12 filter_block">
                                                        <div class="row form-group-block">
                                                            <div class="col-lg-10">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <label class="erp-col-form-label">Filter Name:</label>
                                                                        <div class="erp-select2 report-select2">
                                                                            <select class="form-control erp-form-control-sm report_fields_name" name="listing_studio_default_filter_name">
                                                                                <option value="0">Select</option>
                                                                                @foreach($data['UserFilter'] as $col_name)
                                                                                    <option value="{{$col_name['listing_studio_user_filter_name']}}" {{ $default_filter->name == strtolower($col_name['listing_studio_user_filter_name'])?"selected":"" }}>{{$col_name['listing_studio_user_filter_title']}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-9">
                                                                        <div class="row">
                                                                            <div class="col-lg-4" id="report_filter_types">
                                                                                <label class="erp-col-form-label">Condition:</label>
                                                                                @php
                                                                                    $types = \App\Models\TblSoftFilterType::where('filter_type_data_type_name', $default_filter->filed_type)->where('filter_type_entry_status',1)->get();
                                                                                @endphp
                                                                                <div class="erp-select2 report-select2">
                                                                                    <select class="form-control erp-form-control-sm report_condition" name="listing_studio_default_filter_condition">
                                                                                        @foreach($types as $type)
                                                                                            <option value="{{$type->filter_type_value}}" {{ ($type->filter_type_value == $default_filter->condition)?"selected":"" }}>{{$type->filter_type_title}}</option>
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
                                                                            <input type="hidden" class="report_value_column_type_name" name="listing_studio_default_filter_field_type" value="{{$default_filter->filed_type}}"/>
                                                                            <input type="hidden" class="report_value_column_case_name" name="listing_studio_default_filter_case_name" value="{{$default_filter->case_name}}"/>
                                                                            <div class="col-lg-8" id="report_filter_block">
                                                                                @if($default_filter->filed_type == 'boolean')
                                                                                    <div class="row" id="fields_values"  style="display: none">
                                                                                        <div class="col-lg-12">
                                                                                            <label class="erp-col-form-label">Value:</label>
                                                                                            {{--<input type="text" value="{{ $default_filter->val_1 }}" name="listing_studio_default_filter_value" class="report_value form-control erp-form-control-sm">--}}
                                                                                            <div class="erp-select2 filter-select2">
                                                                                                <select class="form-control erp-form-control-sm report_value" multiple name="listing_studio_default_filter_value" disabled>
                                                                                                    <option value="0">Select</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row" id="number_between"  style="display: none">
                                                                                        <div class="col-lg-12">
                                                                                            <div class="erp-row">
                                                                                                <div class="col-lg-6">
                                                                                                    <label class="erp-col-form-label">From:</label>
                                                                                                    <input disabled type="text" name="listing_studio_default_filter_value" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                </div>
                                                                                                <div class="col-lg-6">
                                                                                                    <label class="erp-col-form-label">To:</label>
                                                                                                    <input disabled type="text" name="listing_studio_default_filter_value_2" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row" id="date_between" style="display: none">
                                                                                        <div class="col-lg-12">
                                                                                            <label class="erp-col-form-label">Select Date Range:</label>
                                                                                            <div class="erp-selectDateRange">
                                                                                                <div class="input-daterange input-group kt_datepicker_5">
                                                                                                    <input disabled type="text" class="form-control erp-form-control-sm" name="listing_studio_default_filter_value" />
                                                                                                    <div class="input-group-append">
                                                                                                        <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                    </div>
                                                                                                    <input disabled type="text" class="form-control erp-form-control-sm" name="listing_studio_default_filter_value_2" />
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif

                                                                                @if($default_filter->filed_type == 'varchar2')
                                                                                    @if($default_filter->condition == 'null' || $default_filter->condition == 'not null')
                                                                                        <div class="row" id="fields_values"  style="display: none">
                                                                                            <div class="col-lg-12">
                                                                                                <label class="erp-col-form-label">Value:</label>
                                                                                                {{--<input type="text" value="{{ $default_filter->val_1 }}" name="listing_studio_default_filter_value" class="report_value form-control erp-form-control-sm">--}}
                                                                                                <div class="erp-select2 filter-select2">
                                                                                                    <select class="form-control erp-form-control-sm report_value" multiple name="listing_studio_default_filter_value" disabled>
                                                                                                        <option value="0">Select</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row" id="number_between"  style="display: none">
                                                                                            <div class="col-lg-12">
                                                                                                <div class="erp-row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <label class="erp-col-form-label">From:</label>
                                                                                                        <input disabled type="text" name="listing_studio_default_filter_value" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <label class="erp-col-form-label">To:</label>
                                                                                                        <input disabled type="text" name="listing_studio_default_filter_value_2" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row" id="date_between" style="display: none">
                                                                                            <div class="col-lg-12">
                                                                                                <label class="erp-col-form-label">Select Date Range:</label>
                                                                                                <div class="erp-selectDateRange">
                                                                                                    <div class="input-daterange input-group kt_datepicker_5">
                                                                                                        <input disabled type="text" class="form-control erp-form-control-sm" name="listing_studio_default_filter_value" />
                                                                                                        <div class="input-group-append">
                                                                                                            <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                        </div>
                                                                                                        <input disabled type="text" class="form-control erp-form-control-sm" name="listing_studio_default_filter_value_2" />
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="row" id="fields_values"  style="display: block">
                                                                                            <div class="col-lg-12">
                                                                                                <label class="erp-col-form-label">Value:</label>
                                                                                                {{--<input type="text" value="{{ $default_filter->val_1 }}" name="listing_studio_default_filter_value" class="report_value form-control erp-form-control-sm">--}}
                                                                                                @php
                                                                                                    if($default_filter->case_name != ''){
                                                                                                        $FilterCase = \App\Models\TblSoftReportingFilterCase::where('reporting_filter_case_id',$default_filter->case_name)->first();
                                                                                                        $data['list'] = \DB::select($FilterCase->reporting_filter_case_query);
                                                                                                        $data['search_type'] = $FilterCase->reporting_filter_case_search_type;
                                                                                                    }
                                                                                                    $col = [];
                                                                                                    foreach($default_filter->val_1 as $val_1){
                                                                                                        array_push($col,$val_1);
                                                                                                    }
                                                                                                @endphp
                                                                                                <div class="erp-select2 filter-select2">
                                                                                                    <select class="form-control erp-form-control-sm report_value" multiple name="listing_studio_default_filter_value">
                                                                                                        <option value="0">Select</option>
                                                                                                        @if(isset($data['search_type']))
                                                                                                            @if($data['search_type'] == 'id')
                                                                                                                @foreach($data['list'] as $list)
                                                                                                                    <option value="{{$list->id}}" {{ (in_array($list->id, $col)) ? 'selected' : '' }}>{{$list->name}}</option>
                                                                                                                @endforeach
                                                                                                            @endif
                                                                                                            @if($data['search_type'] == 'name')
                                                                                                                @foreach($data['list'] as $list)
                                                                                                                    <option value="{{$list->name}}" {{ (in_array($list->name, $col)) ? 'selected' : '' }}>{{$list->name}}</option>
                                                                                                                @endforeach
                                                                                                            @endif
                                                                                                        @endif
                                                                                                        @if($default_filter->case_name == '')
                                                                                                            @foreach($col as $val)
                                                                                                                <option value="{{$val}}" {{ (in_array($val, $col)) ? 'selected' : '' }}>{{$val}}</option>
                                                                                                            @endforeach
                                                                                                        @endif
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row" id="number_between"  style="display: none">
                                                                                            <div class="col-lg-12">
                                                                                                <div class="erp-row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <label class="erp-col-form-label">From:</label>
                                                                                                        <input disabled type="text" name="listing_studio_default_filter_value" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <label class="erp-col-form-label">To:</label>
                                                                                                        <input disabled type="text" name="listing_studio_default_filter_value_2" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row" id="date_between" style="display: none">
                                                                                            <div class="col-lg-12">
                                                                                                <label class="erp-col-form-label">Select Date Range:</label>
                                                                                                <div class="erp-selectDateRange">
                                                                                                    <div class="input-daterange input-group kt_datepicker_5">
                                                                                                        <input disabled type="text" class="form-control erp-form-control-sm" name="listing_studio_default_filter_value" />
                                                                                                        <div class="input-group-append">
                                                                                                            <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                        </div>
                                                                                                        <input disabled type="text" class="form-control erp-form-control-sm" name="listing_studio_default_filter_value_2" />
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                @endif
                                                                                @if($default_filter->filed_type == 'number')
                                                                                    @if($default_filter->condition == 'between')
                                                                                        <div class="row" id="number_between"  style="display: block">
                                                                                            <div class="col-lg-12">
                                                                                                <div class="erp-row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <label class="erp-col-form-label">From:</label>
                                                                                                        <input type="text" value="{{ $default_filter->val_1 }}" name="listing_studio_default_filter_value" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <label class="erp-col-form-label">To:</label>
                                                                                                        <input type="text" value="{{ $default_filter->val_2 }}" name="listing_studio_default_filter_value_2" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row" id="fields_values"  style="display: none">
                                                                                            <div class="col-lg-12">
                                                                                                <label class="erp-col-form-label">Value:</label>
                                                                                                {{--<input type="text" value="{{ $default_filter->val_1 }}" name="listing_studio_default_filter_value" class="report_value form-control erp-form-control-sm">--}}
                                                                                                <div class="erp-select2 filter-select2">
                                                                                                    <select class="form-control erp-form-control-sm report_value" multiple name="listing_studio_default_filter_value" disabled>
                                                                                                        <option value="0">Select</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row" id="date_between" style="display: none">
                                                                                            <div class="col-lg-12">
                                                                                                <label class="erp-col-form-label">Select Date Range:</label>
                                                                                                <div class="erp-selectDateRange">
                                                                                                    <div class="input-daterange input-group kt_datepicker_5">
                                                                                                        <input disabled type="text" class="form-control erp-form-control-sm" name="listing_studio_default_filter_value" />
                                                                                                        <div class="input-group-append">
                                                                                                            <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                        </div>
                                                                                                        <input disabled type="text" class="form-control erp-form-control-sm" name="listing_studio_default_filter_value_2" />
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="row" id="fields_values"  style="display: block">
                                                                                            <div class="col-lg-12">
                                                                                                <label class="erp-col-form-label">Value:</label>
                                                                                                {{--<input type="text" value="{{ $default_filter->val_1 }}" name="listing_studio_default_filter_value" class="report_value form-control erp-form-control-sm">--}}
                                                                                                @php
                                                                                                    if($default_filter->case_name != ''){
                                                                                                        $FilterCase = \App\Models\TblSoftReportingFilterCase::where('reporting_filter_case_id',$default_filter->case_name)->first();
                                                                                                        $data['list'] = \DB::select($FilterCase->reporting_filter_case_query);
                                                                                                        $data['search_type'] = $FilterCase->reporting_filter_case_search_type;
                                                                                                    }
                                                                                                    $col = [];
                                                                                                    foreach($default_filter->val_1 as $val_1){
                                                                                                        array_push($col,$val_1);
                                                                                                    }
                                                                                                @endphp
                                                                                                <div class="erp-select2 filter-select2">
                                                                                                    <select class="form-control erp-form-control-sm report_value" multiple name="listing_studio_default_filter_value">
                                                                                                        <option value="0">Select</option>
                                                                                                        @if(isset($data['search_type']))
                                                                                                            @if($data['search_type'] == 'id')
                                                                                                                @foreach($data['list'] as $list)
                                                                                                                    <option value="{{$list->id}}" {{ (in_array($list->id, $col)) ? 'selected' : '' }}>{{$list->name}}</option>
                                                                                                                @endforeach
                                                                                                            @endif
                                                                                                            @if($data['search_type'] == 'name')
                                                                                                                @foreach($data['list'] as $list)
                                                                                                                    <option value="{{$list->name}}" {{ (in_array($list->name, $col)) ? 'selected' : '' }}>{{$list->name}}</option>
                                                                                                                @endforeach
                                                                                                            @endif
                                                                                                        @endif
                                                                                                        @if(!isset($data['search_type']))
                                                                                                            @foreach($col as $va)
                                                                                                                <option value="{{$va}}" {{ (in_array($va, $col)) ? 'selected' : '' }}>{{$va}}</option>
                                                                                                            @endforeach
                                                                                                        @endif
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row" id="number_between"  style="display: none">
                                                                                            <div class="col-lg-12">
                                                                                                <div class="erp-row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <label class="erp-col-form-label">From:</label>
                                                                                                        <input disabled type="text" name="listing_studio_default_filter_value" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <label class="erp-col-form-label">To:</label>
                                                                                                        <input disabled type="text" name="listing_studio_default_filter_value_2" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row" id="date_between" style="display: none">
                                                                                            <div class="col-lg-12">
                                                                                                <label class="erp-col-form-label">Select Date Range:</label>
                                                                                                <div class="erp-selectDateRange">
                                                                                                    <div class="input-daterange input-group kt_datepicker_5">
                                                                                                        <input disabled type="text" class="form-control erp-form-control-sm" name="listing_studio_default_filter_value" />
                                                                                                        <div class="input-group-append">
                                                                                                            <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                        </div>
                                                                                                        <input disabled type="text" class="form-control erp-form-control-sm" name="listing_studio_default_filter_value_2" />
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                @endif
                                                                                @if($default_filter->filed_type == 'date')
                                                                                    <div class="row" id="date_between" style="display: block">
                                                                                        <div class="col-lg-12">
                                                                                            <label class="erp-col-form-label">Select Date Range:</label>
                                                                                            <div class="erp-selectDateRange">
                                                                                                <div class="input-daterange input-group kt_datepicker_5">
                                                                                                    <input type="text" value="{{ $default_filter->val_1 }}" class="form-control erp-form-control-sm" name="listing_studio_default_filter_value" />
                                                                                                    <div class="input-group-append">
                                                                                                        <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                    </div>
                                                                                                    <input type="text" value="{{ $default_filter->val_2 }}" class="form-control erp-form-control-sm" name="listing_studio_default_filter_value_2" />
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row" id="fields_values"  style="display: none">
                                                                                        <div class="col-lg-12">
                                                                                            <label class="erp-col-form-label">Value:</label>
                                                                                            {{--<input type="text" value="{{ $default_filter->val_1 }}" name="listing_studio_default_filter_value" class="report_value form-control erp-form-control-sm">--}}
                                                                                            <div class="erp-select2 filter-select2">
                                                                                                <select class="form-control erp-form-control-sm report_value" multiple name="listing_studio_default_filter_value" disabled>
                                                                                                    <option value="0">Select</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row" id="number_between"  style="display: none">
                                                                                        <div class="col-lg-12">
                                                                                            <div class="erp-row">
                                                                                                <div class="col-lg-6">
                                                                                                    <label class="erp-col-form-label">From:</label>
                                                                                                    <input disabled type="text" name="listing_studio_default_filter_value" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                </div>
                                                                                                <div class="col-lg-6">
                                                                                                    <label class="erp-col-form-label">To:</label>
                                                                                                    <input disabled type="text" name="listing_studio_default_filter_value_2" class="form-control erp-form-control-sm text-left validNumber">
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
                                                                <a href="javascript:;" class="btn btn-bold btn-sm btn-label-brand report-user-filter-or-btn report-user-filter-or-btn-outer" disabled readonly >
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
                                                            <select class="form-control erp-form-control-sm report_fields_name" name="listing_studio_default_filter_name">
                                                                <option value="0">Select</option>
                                                                @foreach($data['UserFilter'] as $col_name)
                                                                    <option value="{{$col_name['listing_studio_user_filter_name']}}">{{$col_name['listing_studio_user_filter_title']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <div class="row">
                                                            <div class="col-lg-4" id="report_filter_types">
                                                                <label class="erp-col-form-label">Condition:</label>
                                                                <div class="erp-select2 report-select2">
                                                                    <select class="form-control erp-form-control-sm report_condition" name="listing_studio_default_filter_condition">
                                                                        <option value="0">Select</option>
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
                                                            <input type="hidden" class="report_value_column_type_name" name="listing_studio_default_filter_field_type" value=""/>
                                                            <input type="hidden" class="report_value_column_case_name" name="listing_studio_default_filter_case_name" value=""/>
                                                            <div class="col-lg-8" id="report_filter_block">
                                                                <div class="row" id="fields_values">
                                                                    <div class="col-lg-12">
                                                                        <label class="erp-col-form-label">Value:</label>
                                                                        {{--<input type="text" disabled name="listing_studio_default_filter_value" class="report_value form-control erp-form-control-sm">--}}
                                                                        <div class="erp-select2 filter-select2">
                                                                            <select class="form-control erp-form-control-sm report_value" multiple name="listing_studio_default_filter_value" disabled>
                                                                                <option value="0">Select</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row" id="number_between">
                                                                    <div class="col-lg-12">
                                                                        <div class="erp-row">
                                                                            <div class="col-lg-6">
                                                                                <label class="erp-col-form-label">From:</label>
                                                                                <input type="text" disabled name="listing_studio_default_filter_value" class="form-control erp-form-control-sm text-left validNumber">
                                                                            </div>
                                                                            <div class="col-lg-6">
                                                                                <label class="erp-col-form-label">To:</label>
                                                                                <input type="text" disabled name="listing_studio_default_filter_value_2" class="form-control erp-form-control-sm text-left validNumber">
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                <div class="row" id="date_between">
                                                                    <div class="col-lg-12">
                                                                        <label class="erp-col-form-label">Select Date Range:</label>
                                                                        <div class="erp-selectDateRange">
                                                                            <div class="input-daterange input-group kt_datepicker_5">
                                                                                <input type="text" disabled class="form-control erp-form-control-sm" name="listing_studio_default_filter_value" />
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text erp-form-control-sm">To</span>
                                                                                </div>
                                                                                <input type="text" disabled class="form-control erp-form-control-sm" name="listing_studio_default_filter_value_2" />
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
                                                <a href="javascript:;" class="btn btn-bold btn-sm btn-label-brand report-user-filter-or-btn report-user-filter-or-btn-outer" disabled readonly >
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
                @else
                    No filter found.
                @endif
            </div>
            @if(isset($data['UserFilter']) &&  count($data['UserFilter']) != 0)
                <div class="row">
                    <div class="col-lg-12">
                        <button data-repeater-create type="button" class="btn btn-brand btn-sm">AND</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @if(isset($data['UserFilter']) &&  count($data['UserFilter']) != 0)
        <div class="modal-footer">
            <label>
                <input name="disable_filters" type="checkbox" autocomplete="off"> Don't Apply Filters
            </label>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    @endif
</form>
<style>
    .filter-select2>.select2>.selection>.select2-selection>ul.select2-selection__rendered {
        line-height: 1.25 !important;
    }
    .erp-col-form-label{
        padding: 0 !important;
    }
    .report-user-filter-del-btn,
    .report-user-filter-or-btn-outer {
        top: 16px !important;
    }
    .modal-body{
        max-height: 355px;
        overflow-y: auto;
    }
    /*
       scrollbar styling
   */
    .modal-body::-webkit-scrollbar-track
    {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        background-color: #ffffff;
        border-radius: 10px;

    }

    .modal-body::-webkit-scrollbar
    {
        width: 2px;
        background-color: #ffffff;
    }

    .modal-body::-webkit-scrollbar-thumb
    {
        background-color: #7f7f7f;
    }
    .modal-body:hover,
    .modal-body:focus {
        visibility: visible;
    }
</style>
<script>
    $('input').attr('autocomplete', 'off');
    var column_type_name = '';
    var user_case_name = '';
    var KTFormRepeater = function() {
        var kt_user_listing_repeater = function() {
            $('#kt_user_listing_repeater').repeater({
                initEmpty: false,
                isFirstItemUndeletable: true,
                repeaters: [{
                    // (Required)
                    // Specify the jQuery selector for this nested repeater
                    selector: '.inner-repeater',
                    isFirstItemUndeletable: true,
                    show: function () {
                        $('.report_fields_name').select2({
                            placeholder: "Select"
                        });
                        $('.report_condition').select2({
                            placeholder: "Select"
                        });
                        $('.report_value').select2({
                            placeholder: "Select",
                            tags: true
                        });
                        /* start for -Case Edit- */
                        var filter_block_len = $(this).find('.filter_block').length
                        for(var i=0; i < filter_block_len ; i++){
                            $(this).find('.filter_block:eq(1)').remove();
                        }
                        $(this).find(".report_fields_name ").val(-1).trigger('change');
                        /* end for -Case Edit- */
                        $(this).slideDown();
                    },
                    ready: function (setIndexes) {
                        var arrows = {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        }
                        $('.kt_datepicker_5').datepicker({
                            rtl: KTUtil.isRTL(),
                            todayHighlight: true,
                            format:'dd-mm-yyyy',
                            templates: arrows
                        });
                    },
                    hide: function (deleteElement) {
                        $(this).slideUp(deleteElement);
                    }
                }],
                show: function () {
                    // $(this).find('.report_fields_name').html(cloumnsList);
                    $('.report_fields_name').select2({
                        placeholder: "Select"
                    });
                    $('.report_condition').select2({
                        placeholder: "Select"
                    });
                    $('.report_value').select2({
                        placeholder: "Select",
                        tags: true
                    });
                    /* start for -Case Edit- */
                    var filter_block_len = $(this).find('.filter_block').length
                    for(var i=1; i < filter_block_len ; i++){
                        $(this).find('.filter_block:eq(1)').remove();
                    }
                    $(this).find(".report_fields_name").val(-1).trigger('change');
                    /* end for -Case Edit- */
                    $(this).slideDown();
                },
                ready:function(){
                    $('.report_fields_name').select2({
                        placeholder: "Select"
                    });
                    $('.report_condition').select2({
                        placeholder: "Select"
                    });
                    $('.report_value').select2({
                        placeholder: "Select",
                        tags: true
                    });
                },
                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            });
        }
        return {
            // public functions
            init: function() {
                kt_user_listing_repeater();
            }
        };
    }();
    jQuery(document).ready(function() {
        KTFormRepeater.init();

    });
    $(document).on('change', '.report_fields_name', function(event) {
        var that = $(this);
        var table_name = $('.listing_studio_table_name').val();
        var val = $(this).val();
        console.log("val: " + val);
        if(val == "" || val == 0 || val == null){
            that.parents('.filter_block').find('.report_condition').html('<option value="">Select</option>');;
            hideData(that)
            return false;
        }
        var url = '/listing-studio/get-filed-conditions/'+casetype+'/'+val;
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'GET',
            url: url,
            data:{_token: CSRF_TOKEN},
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            success: function(response, status){
                console.log(response);
                if(response.status == 'success') {
                    var FiledConditionsList = '';
                    var data = response['data']['condition_arr'];
                    column_type_name = data[0]['filter_type_data_type_name'].toLowerCase();
                    user_case_name = response['data']['case_name'];
                    FiledConditionsList += '<option value="0">Select</option>';
                    if(data != ""){
                        for(var i=0; data.length > i; i++){
                            FiledConditionsList += '<option value="'+data[i]['filter_type_value'].toLowerCase()+'">'+data[i]['filter_type_title']+'</option>';
                        }
                    }

                    that.parents('.filter_block').find('.report_condition').html(FiledConditionsList);
                    that.parents('.filter_block').find('.report_value_column_type_name').val(column_type_name);
                    that.parents('.filter_block').find('.report_value_column_case_name').val(user_case_name);

                    $('.report_condition').select2({
                        placeholder: "Select"
                    });
                    hideData(that);
                    //  toastr.success(response.message);
                }
                else{
                    toastr.error(response.message);
                }
            },
            error: function(response,status) {
                // console.log(response);
            },
        });

        $('.modal-body').removeAttr('data-select2-id');
    });
    $(document).on('change', '.report_condition', function(event) {
        //debugger
        var that = $(this);
        var val = $(this).val();
        console.log(column_type_name);
        console.log("user_case_name: " + user_case_name);
        if(column_type_name == 'varchar2'){
            hideData(that);
            if(user_case_name !== null && user_case_name !== ''){
                updateDataByCaseName(that,user_case_name);
            }else{
                var optionsList = '<option value="0">Select</option>';
                that.parents('.filter_block').find('#fields_values').find('select').attr('disabled',false);
                that.parents('.filter_block').find('#fields_values').show();
                that.parents('.filter_block').find('#fields_values').find('select').html(optionsList);
                $('.report_value ').select2({
                    placeholder: "Select",
                    tags:true
                });
            }
        }
        if(column_type_name == 'number' && val == 'between'){
            hideData(that);
            that.parents('.filter_block').find('#number_between').find('input').attr('disabled',false);
            that.parents('.filter_block').find('#number_between').show();
        }
        if(column_type_name == 'number' && (val == '=' || val == '!=' || val == '=' || val == '<' || val == '>' || val == '>=' || val == '<=')){
            hideData(that);
            that.parents('.filter_block').find('#fields_values').find('input').attr('disabled',false);
            that.parents('.filter_block').find('#fields_values').show();
        }
        if(column_type_name == 'date' && val == 'between'){
            hideData(that);
            that.parents('.filter_block').find('#date_between').find('input').attr('disabled',false);
            that.parents('.filter_block').find('#date_between').show();
        }
        if(val == 'null' || val == 'not null' || val == 'yes' || val == 'no' || val == 0 || val == ''){
            hideData(that);
        }
        $('.validNumber').keypress(validateNumber);

        // range picker
        var arrows = {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        }
        $('.kt_datepicker_5').datepicker({
            rtl: KTUtil.isRTL(),
            todayHighlight: true,
            format:'dd-mm-yyyy',
            templates: arrows
        });
        $('.modal-body').removeAttr('data-select2-id');
    });
    function hideData(that){
        that.parents('.filter_block').find('#report_filter_block').find('input').attr('disabled',true);
        that.parents('.filter_block').find('#report_filter_block').find('.row').hide();
        var optionsList = '<option value="0">Select</option>';
        that.parents('.filter_block').find('#fields_values').find('select').attr('disabled',false);
        that.parents('.filter_block').find('#fields_values').hide();
        that.parents('.filter_block').find('#fields_values').find('select').html(optionsList);
    }
    function validateNumber(event) {
        var key = window.event ? event.keyCode : event.which;
        if (event.keyCode === 8 || event.keyCode === 46) {
            return true;
        } else if ( key < 48 || key > 57 ) {
            return false;
        } else {
            return true;
        }
    }
    function closeUserFilterModal(){
        $('#kt_modal_1').find('.modal-content').empty();
        $('#kt_modal_1').find('.modal-content').html(' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
        $('#kt_modal_1').modal('hide');
    }
    function updateDataByCaseName(that,user_case_name){
        var url = '/listing-studio/get-filed-data/'+user_case_name;
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'GET',
            url: url,
            data:{_token: CSRF_TOKEN},
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            success: function(response, status){
                console.log(response);
                if(response.status == 'success') {
                    $('.modal-body').removeAttr('data-select2-id');

                    var optionsList = '<option value="0">Select</option>';
                    var data_case =  response['data']['list'];
                    var search_type =  response['data']['search_type'];
                    if(data_case != undefined) {
                        for (var i = 0; data_case.length > i; i++) {
                            if(search_type == 'id'){
                                optionsList += '<option value="'+data_case[i]['id']+'">'+data_case[i]['name']+'</option>';
                            }
                        }
                    }
                    that.parents('.filter_block').find('#fields_values').find('select').attr('disabled',false);
                    that.parents('.filter_block').find('#fields_values').show();
                    that.parents('.filter_block').find('#fields_values').find('select').html(optionsList);
                    $('.report_value ').select2({
                        placeholder: "Select",
                        tags:true
                    });
                }
                else{
                    toastr.error(response.message);
                }
            },
            error: function(response,status) {
                // console.log(response);
            },
        });
    }
</script>
