@extends('layouts.layout')
@section('title', 'Reports')

@section('pageCSS')
@endsection
@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $date =  date('d-m-Y');
                $code =  $data['report_code'];
                $static_dynamic = 'static';
            }
            if($case == 'edit'){
                $id = $data['current']->report_id;
                $code = $data['current']->report_code;
                $date =  $data['current']->report_date;
                $title =  $data['current']->report_title;
                $static_dynamic =  $data['current']->report_static_dynamic;
                $case_name =  $data['current']->report_case;
            }
    @endphp
    <form id="report_form" class="kt-form" method="post" action="{{ action('Report\ReportsController@store',isset($id)?$id:"") }}" enctype="multipart/form-data">
        @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">

                                @if($case == 'edit')
                                    <a href="/reports/report-create/{{$static_dynamic}}/{{$case_name}}" target="_blank" title="report link">
                                        <i class="la la-link"></i> {{isset($code)?$code:""}}
                                    </a>
                                @else
                                    {{isset($code)?$code:""}}
                                @endif
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <div class="input-group date">
                                <input type="text" name="report_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{$date}}" id="kt_datepicker_3" />
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="la la-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="row form-group-block">
                            <div class="col-lg-3">
                                <label class="erp-col-form-label">Title: <span class="required">*</span></label>
                                <input type="text" name="report_title" class="form-control erp-form-control-sm report_title" value="{{isset($title)?$title:""}}"/>
                            </div>
                            <div class="col-lg-3">
                                <label class="erp-col-form-label">Case Name: <span class="required">*</span></label>
                                @if($case == 'edit')
                                    <input type="hidden" name="report_case" value="{{ $case_name }}"/>
                                    <input type="text" id="report_case" class="form-control erp-form-control-sm" value="{{ $case_name }}" disabled/>
                                @else
                                    <input type="text" name="report_case" id="report_case" class="form-control erp-form-control-sm"/>
                                @endif
                            </div>
                            <div class="col-lg-3">
                                <label class="erp-col-form-label">Report Type: <span class="required">*</span></label>
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="report_static_dynamic" name="report_static_dynamic">
                                        <option value="static" {{$static_dynamic == 'static'?"selected":""}}>Static</option>
                                        <option value="dynamic" {{$static_dynamic == 'dynamic'?"selected":""}}>Dynamic</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label class="erp-col-form-label">Select Menu: <span class="required">*</span></label>
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm menu_dtl_id" name="menu_dtl_id">
                                        <option value="0">Select</option>
                                        @foreach($data['report_menu'] as $report_menu)
                                            @php
                                                $current_menu_dtl_id = isset($data['current']->parent_menu_id)?$data['current']->parent_menu_id:"";
                                            @endphp
                                            <option value="{{$report_menu->menu_dtl_id}}" {{$current_menu_dtl_id == $report_menu->menu_dtl_id ?"selected":""}}>{{$report_menu->menu_dtl_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="static_criteria">
                    <div class="kt-portlet" id="static_user_criteria_repeater">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    Select Static Criteria
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="user_criteria">
                                <div class="row">
                                    @if($case == 'edit')
                                        @php
                                            $selected_criteria = explode(",",$data['current']->report_static_criteria);
                                        @endphp
                                    @else
                                        @php
                                            $selected_criteria = [];
                                        @endphp
                                    @endif
                                    @foreach($data['static_criteria'] as $static_criteria)
                                        <div class="col-lg-3">
                                            <div class="kt-checkbox-inline">
                                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                                    <input type="checkbox" name="report_static_criteria[]" value="{{$static_criteria->report_static_criteria_id}}" {{ in_array($static_criteria->report_static_criteria_id,$selected_criteria)?"checked":"" }}> {{$static_criteria->report_static_criteria_title}}
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $static_display = 'none';
                    $dynamic_display = 'none';
                    if($static_dynamic == 'static'){
                        $static_display = 'block';
                    }
                    if($static_dynamic == 'dynamic'){
                        $dynamic_display = 'block';
                    }
                @endphp
                @include('reports.dynamic_criteria')
            </div>
        </div>
    </form>
@endsection
@section('pageJS')

@endsection
@section('customJS')
    <script src="{{ asset('js/pages/js/report.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/report-data-repeater.js') }}" type="text/javascript"></script>
    <script>
        $('.kt-select2').select2({
            placeholder: "Select"
        });

        // Handle Field Type change for Conditional Logic to populate Condition dropdown
        $(document).on('change', '.conditional_logic_field_type_select', function(event) {
            var that = $(this);
            var fieldType = $(this).val();
            var conditionSelect = that.closest('.conditional-logic_block, .outer-conditional-logic_block').find('.conditional_logic_condition');
            var hiddenFieldType = that.closest('.conditional-logic_block, .outer-conditional-logic_block').find('.conditional_logic_field_type');

            if(fieldType == "" || fieldType == "0"){
                conditionSelect.html('<option value="">Select</option>');
                if(hiddenFieldType.length > 0){
                    hiddenFieldType.val('');
                }
                return false;
            }

            // Map column types to filter type data type names
            var typeMapping = {
                'varchar2': 'varchar2',
                'number': 'number',
                'float': 'number', // float maps to number for filter types
                'date': 'date',
                'boolean': 'boolean'
            };

            var filterTypeName = typeMapping[fieldType] || fieldType;

            // Update hidden field type
            if(hiddenFieldType.length > 0){
                hiddenFieldType.val(fieldType);
            }

            var url = '/reports/get-column-conditions';
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: CSRF_TOKEN,
                    col_type: filterTypeName
                },
                contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                success: function(response, status){
                    if(response.status == 'success') {
                        var FiledConditionsList = '';
                        var data = response['data']['conditions'];
                        FiledConditionsList += '<option value="">Select</option>';
                        if(data != "" && data.length > 0){
                            for(var i=0; data.length > i; i++){
                                FiledConditionsList += '<option value="'+data[i]['filter_type_value'].toLowerCase()+'">'+data[i]['filter_type_title']+'</option>';
                            }
                        }
                        conditionSelect.html(FiledConditionsList);

                        // Set saved condition value if exists
                        var savedCondition = conditionSelect.data('saved-value') || conditionSelect.closest('.conditional-logic_block, .outer-conditional-logic_block').find('.conditional_logic_saved_condition').val();
                        if (savedCondition && savedCondition != '') {
                            conditionSelect.val(savedCondition.toLowerCase());
                        }

                        conditionSelect.select2({
                            placeholder: "Select"
                        });

                        // Update fields visibility based on condition
                        var currentCondition = conditionSelect.val();
                        if (currentCondition && currentCondition != '') {
                            updateConditionalLogicFields(conditionSelect);
                        }
                    }
                    else{
                        conditionSelect.html('<option value="">Select</option>');
                        updateConditionalLogicFields(conditionSelect);
                    }
                },
                error: function(response,status) {
                    conditionSelect.html('<option value="">Select</option>');
                    updateConditionalLogicFields(conditionSelect);
                },
            });
        });

        function updateConditionalLogicFields(conditionSelect) {
            var that = conditionSelect;
            var condition = that.val();
            var block = that.closest('.conditional-logic_block, .outer-conditional-logic_block');
            var fieldType = block.find('.conditional_logic_field_type').val();

            var fieldsValues = block.find('.conditional_logic_fields_values');
            var numberBetween = block.find('.conditional_logic_number_between');
            var dateBetween = block.find('.conditional_logic_date_between');

            if (!fieldType || fieldType == '') {
                fieldsValues.hide();
                numberBetween.hide();
                dateBetween.hide();
                fieldsValues.find('input').attr('disabled', true);
                numberBetween.find('input').attr('disabled', true);
                dateBetween.find('input').attr('disabled', true);
                return;
            }

            fieldsValues.find('input').attr('disabled', true);
            numberBetween.find('input').attr('disabled', true);
            dateBetween.find('input').attr('disabled', true);

            fieldsValues.hide();
            numberBetween.hide();
            dateBetween.hide();

            if (condition == '' || condition == 'null' || condition == 'not null') {
                return;
            }

            if (fieldType == 'boolean' || condition == 'yes' || condition == 'no') {
                fieldsValues.hide();
            } else if (fieldType == 'number' && condition == 'between') {
                numberBetween.show();
                numberBetween.find('input').attr('disabled', false);
            } else if (fieldType == 'date' && condition == 'between') {
                dateBetween.show();
                dateBetween.find('input').attr('disabled', false);

                var arrows = {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                };
                dateBetween.find('.kt_datepicker_5').datepicker({
                    rtl: KTUtil.isRTL(),
                    todayHighlight: true,
                    format: 'dd-mm-yyyy',
                    templates: arrows
                });
            } else {
                fieldsValues.show();
                fieldsValues.find('input').attr('disabled', false);
            }
        }

        $(document).on('change', '.conditional_logic_condition', function(event) {
            updateConditionalLogicFields($(this));
        });

        $(document).ready(function() {
            function syncConditionalLogicButtons(scope) {
                var $scope = scope ? $(scope) : $(document);

                // AND groups: hide delete for first outer group, show for others
                $scope.find('#kt_repeater_conditional_logic [data-repeater-list="outer_conditional_logic"] > [data-repeater-item]').each(function(idx){
                    var $group = $(this);
                    var $andDel = $group.find('.report-conditional-logic-and-del-btn').first();
                    var $andDown = $group.find('.conditional-logic-and-down').first();
                    if (idx === 0) {
                        $andDel.css('display','none');
                        $andDown.css('display','none');
                    } else {
                        $andDel.css('display','inline-block');
                        $andDown.css('display','inline-block');
                    }
                });

                $scope.find('.inner-repeater-conditional-logic').each(function() {
                    var $inner = $(this);
                    var $items = $inner.find('[data-repeater-list] > [data-repeater-item]');
                    $items.each(function(idx) {
                        var $item = $(this);
                        var $del = $item.find('.conditional-logic-del-btn').first();
                        if (!$del.length) return;
                        if (idx === 0) {
                            $del.css('display', 'none');
                        } else {
                            $del.css('display', 'inline-block');
                        }
                    });
                });
            }

            // Initialize conditional logic repeater if it exists
            if ($('#kt_repeater_conditional_logic').length > 0) {
                $('#kt_repeater_conditional_logic').repeater({
                    initEmpty: false,
                    isFirstItemUndeletable: true,
                    repeaters: [{
                        selector: '.inner-repeater-conditional-logic',
                        isFirstItemUndeletable: true,
                        show: function () {
                            $(this).slideDown();
                            syncConditionalLogicButtons($(this).closest('#kt_repeater_conditional_logic'));
                        },
                        hide: function (deleteElement) {
                            $(this).slideUp(deleteElement);
                            syncConditionalLogicButtons($(this).closest('#kt_repeater_conditional_logic'));
                        }
                    }],
                    show: function() {
                        $(this).slideDown();
                        syncConditionalLogicButtons($(this).closest('#kt_repeater_conditional_logic'));
                    },
                    hide: function(deleteElement) {
                        $(this).slideUp(deleteElement);
                        syncConditionalLogicButtons($(this).closest('#kt_repeater_conditional_logic'));
                    }
                });

                // Ensure correct state on first render (edit mode) as well
                syncConditionalLogicButtons($('#kt_repeater_conditional_logic'));
            }

            // Initialize saved conditional logic values on page load
            setTimeout(function() {
                $('.conditional-logic_block, .outer-conditional-logic_block').each(function() {
                    var block = $(this);
                    var fieldTypeSelect = block.find('.conditional_logic_field_type_select');
                    var fieldType = fieldTypeSelect.val();
                    var savedCondition = block.find('.conditional_logic_saved_condition').val() || block.find('.conditional_logic_condition').data('saved-value') || '';
                    var conditionSelect = block.find('.conditional_logic_condition');

                    // If field type is set, populate conditions and set saved value
                    if (fieldType && fieldType != '' && fieldType != '0') {
                        // Trigger change to populate condition dropdown
                        fieldTypeSelect.trigger('change');

                        // After AJAX completes, set the saved condition value
                        var checkCondition = setInterval(function() {
                            if (conditionSelect.find('option').length > 1) {
                                clearInterval(checkCondition);
                                if (savedCondition && savedCondition != '') {
                                    conditionSelect.val(savedCondition.toLowerCase()).trigger('change');
                                    conditionSelect.select2({
                                        placeholder: "Select"
                                    });
                                }
                            }
                        }, 100);

                        // Timeout after 2 seconds
                        setTimeout(function() {
                            clearInterval(checkCondition);
                        }, 2000);
                    }
                });

                // Also handle existing conditions
                $('.conditional_logic_condition').each(function() {
                    if ($(this).val() != '') {
                        updateConditionalLogicFields($(this));
                    }
                });
            }, 100);
        });

        // OR add is handled by the single bottom-right OR button (data-repeater-create) inside each AND group
        // AND add: explicitly trigger outer repeater create to avoid nested-repeater ambiguity
        $(document).on('click', '.js-conditional-logic-add-and-btn', function(e) {
            e.preventDefault();
            var $wrap = $(this).closest('#kt_repeater_conditional_logic');
            var $create = $wrap.find('> .js-conditional-logic-add-and-create[data-repeater-create]').first();
            if ($create.length) {
                $create.trigger('click');
            }
        });

        $('#report_static_dynamic').on('change', function() {
            if($(this).val() == 'static'){
                $('#report_form').find('#dynamic_criteria').hide();
                $('#dynamic_criteria').find('input').attr('disabled', true);
                $('#dynamic_criteria').find('select').attr('disabled', true);
            }
            if($(this).val() == 'dynamic'){
                $('#report_form').find('#dynamic_criteria').show();
                $('#dynamic_criteria').find('input').attr('disabled', false);
                $('#dynamic_criteria').find('select').attr('disabled', false);
            }
        });
        $('.report_table_style_layout').on('change', function() {
            if($(this).val() == 'listing'){
                $('#report_form').find('#report_data_group_keys').hide();
            }
            if($(this).val() == 'listing_group'){
                $('#report_form').find('#report_data_group_keys').show();
            }
        });

        $(document).on('click','.sel-col-align',function(){
            var dataValue = $(this).find('i').attr("data-value");
            $(this).parents('.column-align').find('input.column_align_val').val(dataValue);
            $(this).parents('.column-align').find('.fa').removeClass('fa-active');
            $(this).find('.fa').addClass('fa-active');
        });
        $(document).on('change','.report_dynamic_column_type',function(){
            let val = $(this).val();
            var parent = $(this).parents('.user_criteria_block');
            if(val == 'number'){
                parent.find('#report_dynamic_decimal_block').hide();
                parent.find('#report_dynamic_calculation_block').show();
            }
            if(val == 'float'){
                parent.find('#report_dynamic_decimal_block').show();
                parent.find('#report_dynamic_calculation_block').show();
            }
            if(val == 'varchar2' || val == 'date' || val == 0){
                parent.find('#report_dynamic_decimal_block').hide();
                parent.find('#report_dynamic_calculation_block').hide();
            }
            parent.find('#report_dynamic_decimal_block>input').val("");
            parent.find('#report_dynamic_calculation_block input').prop('checked',false).attr('checked',false);
        });
        $(document).on('dblclick','.dynamic_variable_list>div',function(){
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(this).text()).select();
            document.execCommand("copy");
            $temp.remove();
            $('.dynamic_variable_copied').fadeToggle("slow");
            $('.dynamic_variable_copied').fadeOut("slow");
         });
    </script>
@endsection
