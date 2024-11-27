@php
    if($case == 'new'){
        $sr = "";
        $qry = "";
        $selected_style_listing = 'listing';
        $elements = [];
    }
    if($case == 'edit'){
        $sr = ($data['current']->report_column_sr_no == 1)?"checked":"";
        $selected_style_listing = ($data['current']->report_table_style_layout != "")?$data['current']->report_table_style_layout:"listing";
        $qry = $data['current']->report_query;
        $grouping_keys = explode(',',$data['current']->report_data_grouping_keys);
        $styles = isset($data['current']->report_styling)?$data['current']->report_styling:[];
        $ThStyles = [];
        $TdStyles = [];
        $elements = [];
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

       /* dump(count($elements));
        dd($elements);*/
    }
  //  dd($elements);
@endphp
<div id="dynamic_criteria" style="display: {{$dynamic_display}}">
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Dynamic Details
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Style Listing:</label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="form-control erp-form-control-sm report_table_style_layout" name="report_table_style_layout">
                                            @foreach($data['style_listing'] as $style_listing_key=>$style_listing)
                                                <option value="{{$style_listing_key}}" {{$selected_style_listing==$style_listing_key?"selected":""}}>{{$style_listing}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Serial No:</label>
                                <div class="col-lg-6 kt-checkbox-inline">
                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                        <input type="checkbox" name="report_column_sr" {{$sr}}>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /row --}}
                    <div class="row form-group-block">
                        <div class="col-lg-12">
                            <label class="erp-col-form-label">Query:</label>
                            <textarea class="form-control erp-form-control-sm" name="dynamic_query" id="dynamic_query" rows="15">{{$qry}}</textarea>
                        </div>
                    </div>{{-- /row --}}
                    @php
                        if($selected_style_listing == 'listing_group'){
                            $group_keys_block = "";
                        }else{
                            $group_keys_block = "none";
                        }
                    @endphp
                    <div class="row kt-margin-t-15" id="report_data_group_keys" style="display: {{$group_keys_block}}">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="erp-col-form-label col-lg-4">Group by 1: <span class="required">*</span></label>
                                <input type="text" value="{{isset($grouping_keys[0])?$grouping_keys[0]:""}}" class="col-lg-8 form-control erp-form-control-sm" name="group_key_1">
                            </div>
                            <div class="row">
                                <label class="erp-col-form-label col-lg-4">Field is Date:</label>
                                <div class="col-lg-8 kt-checkbox-inline">
                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                        <input type="checkbox" name="group_key_1_date" {{isset($grouping_keys[1]) && ($grouping_keys[1] == 1)?"checked":""}}>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="erp-col-form-label col-lg-4">Group by 2: <span class="required">*</span></label>
                                <input type="text" value="{{isset($grouping_keys[2])?$grouping_keys[2]:""}}" class="col-lg-8 form-control erp-form-control-sm" name="group_key_2">
                            </div>
                            <div class="row">
                                <label class="erp-col-form-label col-lg-4">Field is Date:</label>
                                <div class="col-lg-8 kt-checkbox-inline">
                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                        <input type="checkbox" name="group_key_2_date" {{isset($grouping_keys[3]) && ($grouping_keys[3] == 1)?"checked":""}}>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /row --}}
                </div>
                <div class="col-lg-3" id="dynamic_variable">
                    <div class="dynamic_variable_copied" style="display: none;">Copied</div>
                    <div class="dynamic_variable_list">
                        <div>$branch_multiple$</div>
                        <div>$from_date$</div>
                        <div>$to_date$</div>
                        <div>$date$</div>

                        <div>$chart_account$</div>
                        <div>$chart_account_multiple$</div>

                        <div>$customer_group_multiple$</div>
                        <div>$customer_multiple$</div>

                        <div>$payment_types$</div>
                        <div>$rate_type$</div>
                        <div>$sales_type$</div>
                        <div>$sales_type_multiple$</div>
                        <div>$salesman$</div>

                        <div>$supplier_group_multiple$</div>
                        <div>$supplier_multiple$</div>

                        <div>$product_group_multiple$</div>
                        <div>$product$</div>
                        <div>$product_multiple$</div>

                        <div>$store_multiple$</div>

                        <div>$voucher_type_multiple$</div>
                    </div>

                    <div class="dynamic_variable_list">
                        <div>$whereWithdynamicqry$</div>
                        <div>$withdynamicqry$</div>
                        <div>$andwithdynamicqry$</div>
                        <div>$f_product_group_id$</div>
                        <div>$f_product_group_name$</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="kt-portlet" id="dynamic_user_criteria_repeater">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Dynamic Criteria
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div data-repeater-list="user_criteria">
                @foreach($elements as $k=>$item)
                    <div data-repeater-item class="user_criteria_block" style="border-bottom: 1px dashed #fb0000;padding-bottom: 5px;margin-bottom: 15px;">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h6>Column Detail:</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="erp-col-form-label col-lg-4">Column Show:</label>
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                                    <input type="checkbox" name="column_toggle" class="column_toggle" {{isset($elements[$k]['column_toggle']) && ($elements[$k]['column_toggle'] == 1)?"checked":""}}>
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="erp-col-form-label">Criteria Active:</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                                    <input type="checkbox" name="criteria_active" class="criteria_active" {{isset($elements[$k]['criteria_active']) && ($elements[$k]['criteria_active'] == 1)?"checked":""}}>
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="erp-col-form-label col-lg-4">Heading Name:</label>
                                    <input type="text" value="{{isset($elements[$k]['heading_name'])?$elements[$k]['heading_name']:""}}" class="col-lg-8 form-control erp-form-control-sm report_dynamic_heading_name" name="report_dynamic_heading_name">
                                </div>
                                <div class="row">
                                    <label class="erp-col-form-label col-lg-4">Key Name:</label>
                                    <input type="text" value="{{isset($elements[$k]['key_name'])?$elements[$k]['key_name']:""}}" class="col-lg-8 form-control erp-form-control-sm report_dynamic_key_name" name="report_dynamic_key_name">
                                </div>
                                <div class="row">
                                    <label class="erp-col-form-label col-lg-4">Column Type:</label>
                                    <div class="col-lg-8 kt-padding-0">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm report_dynamic_column_type" name="report_dynamic_column_type">
                                                <option value="0">Select</option>
                                                @foreach($data['column_types'] as $key=>$column_types)
                                                    <option value="{{$key}}" {{$elements[$k]['column_type']==$key?"selected":""}}>{{$column_types}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    if($elements[$k]['column_type'] == 'float' || $elements[$k]['decimal'] != ""){
                                        $deci_block = "";
                                    }else{
                                        $deci_block = "none";
                                    }
                                @endphp
                                <div class="row" id="report_dynamic_decimal_block" style="display: {{$deci_block}}">
                                    <label class="col-lg-4 erp-col-form-label">Decimal:</label>
                                    <input type="text" value="{{isset($elements[$k]['decimal'])?$elements[$k]['decimal']:""}}" class="col-lg-8 form-control erp-form-control-sm validNumber report_dynamic_decimal" name="report_dynamic_decimal">
                                </div>
                                @php
                                    if($elements[$k]['column_type'] == 'float' || $elements[$k]['column_type'] == 'number' || (isset($elements[$k]['calc']) && ($elements[$k]['calc'] == 1))){
                                        $calc_block = "";
                                    }else{
                                        $calc_block = "none";
                                    }
                                @endphp
                                <div class="row" id="report_dynamic_calculation_block" style="display: {{$calc_block}}">
                                    <label class="col-lg-4 erp-col-form-label">Calculation:</label>
                                    <div class="col-lg-8 kt-checkbox-inline">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                            <input type="checkbox" name="report_dynamic_calculation" {{isset($elements[$k]['calc']) && ($elements[$k]['calc'] == 1)?"checked":""}}>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h6>Table Heading:</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Column Align:</label>
                                    <div class="col-lg-6 kt-padding-0">
                                        <div class="row text-center column-align">
                                            <input type="hidden" class="column_align_val" value="{{isset($ThStyles[$k]['text-align'])?$ThStyles[$k]['text-align']:"left"}}" name="report_dynamic_heading_style_column_align">
                                            <div class="col-lg-4 sel-col-align">
                                                <i class="fa fa-align-left {{(isset($ThStyles[$k]['text-align']) && $ThStyles[$k]['text-align']=='left')?"fa-active":""}}" data-value="left"></i>
                                            </div>
                                            <div class="col-lg-4 sel-col-align">
                                                <i class="fa fa-align-center {{(isset($ThStyles[$k]['text-align']) && $ThStyles[$k]['text-align']=='center')?"fa-active":""}}" data-value="center"></i>
                                            </div>
                                            <div class="col-lg-4 sel-col-align">
                                                <i class="fa fa-align-right {{(isset($ThStyles[$k]['text-align']) && $ThStyles[$k]['text-align']=='right')?"fa-active":""}}" data-value="right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Font Size:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="text" value="{{isset($ThStyles[$k]['font-size'])?str_replace("px","",$ThStyles[$k]['font-size']):""}}" name="report_dynamic_heading_style_font_size" class="validNumber text-left form-control erp-form-control-sm">
                                        <div class="input-group-append">
                                            <span class="input-group-text erp-form-control-sm">
                                                px
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Color:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="color" name="report_dynamic_heading_style_color" class="form-control erp-form-control-sm" value="{{isset($ThStyles[$k]['color'])?$ThStyles[$k]['color']:"#e2e5ec"}}">
                                        <div class="input-group-append">
                                            <span class="input-group-text erp-form-control-sm">
                                                <label class="kt-checkbox kt-checkbox--single">
                                                    <input class="transparent" name="report_dynamic_heading_style_color_transparent" type="checkbox" {{!isset($ThStyles[$k]['color'])?"checked":""}}>
                                                    <span></span>
                                                </label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Background Color:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="color" name="report_dynamic_heading_style_bgcolor" class="form-control erp-form-control-sm" value="{{isset($ThStyles[$k]['background-color'])?$ThStyles[$k]['background-color']:"#e2e5ec"}}">
                                        <div class="input-group-append">
                                            <span class="input-group-text erp-form-control-sm">
                                                <label class="kt-checkbox kt-checkbox--single">
                                                    <input class="transparent" name="report_dynamic_heading_style_bgcolor_transparent" type="checkbox" {{!isset($ThStyles[$k]['background-color'])?"checked":""}}>
                                                    <span></span>
                                                </label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Width:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="text" value="{{isset($ThStyles[$k]['width'])?str_replace("px","",$ThStyles[$k]['width']):""}}" name="report_dynamic_heading_style_width" class="validNumber text-left form-control erp-form-control-sm">
                                        <div class="input-group-append">
                                            <span class="input-group-text erp-form-control-sm">
                                                px
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h6>Table Body:</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Column Align:</label>
                                    <div class="col-lg-6 kt-padding-0">
                                        <div class="row text-center column-align">
                                            <input type="hidden" class="column_align_val" value="{{isset($TdStyles[$k]['text-align'])?$TdStyles[$k]['text-align']:"left"}}" name="report_dynamic_body_style_column_align">
                                            <div class="col-lg-4 sel-col-align">
                                                <i class="fa fa-align-left {{isset($TdStyles[$k]['text-align'])&&$TdStyles[$k]['text-align']=='left'?"fa-active":""}}" data-value="left"></i>
                                            </div>
                                            <div class="col-lg-4 sel-col-align">
                                                <i class="fa fa-align-center {{isset($TdStyles[$k]['text-align'])&&$TdStyles[$k]['text-align']=='center'?"fa-active":""}}" data-value="center"></i>
                                            </div>
                                            <div class="col-lg-4 sel-col-align">
                                                <i class="fa fa-align-right {{isset($TdStyles[$k]['text-align'])&&$TdStyles[$k]['text-align']=='right'?"fa-active":""}}" data-value="right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Font Size:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="text" value="{{isset($TdStyles[$k]['font-size'])?str_replace("px","",$TdStyles[$k]['font-size']):""}}" name="report_dynamic_body_style_font_size" class="validNumber text-left form-control erp-form-control-sm">
                                        <div class="input-group-append">
                                            <span class="input-group-text erp-form-control-sm">
                                                px
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Color:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="color" name="report_dynamic_body_style_color" class="form-control erp-form-control-sm" value="{{isset($TdStyles[$k]['color'])?$TdStyles[$k]['color']:"#e2e5ec"}}">
                                        <div class="input-group-append">
                                            <span class="input-group-text erp-form-control-sm">
                                                <label class="kt-checkbox kt-checkbox--single">
                                                    <input class="transparent" name="report_dynamic_body_style_color_transparent" type="checkbox" {{!isset($TdStyles[$k]['color'])?"checked":""}}>
                                                    <span></span>
                                                </label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Background Color:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="color" name="report_dynamic_body_style_bgcolor" class="form-control erp-form-control-sm" value="{{isset($TdStyles[$k]['background-color'])?$TdStyles[$k]['background-color']:"#e2e5ec"}}">
                                        <div class="input-group-append">
                                            <span class="input-group-text erp-form-control-sm">
                                                <label class="kt-checkbox kt-checkbox--single">
                                                    <input name="report_dynamic_body_style_bgcolor_transparent" class="transparent" type="checkbox" {{!isset($TdStyles[$k]['background-color'])?"checked":""}}>
                                                    <span></span>
                                                </label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-9"></div>
                            <div class="col-lg-3 text-right">
                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger report-filter-del-btn" style="top: 0px;">
                                    <i class="la la-trash-o"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if(count($elements) == 0)
                    <div data-repeater-item class="user_criteria_block" style="border-bottom: 1px dashed #fb0000;padding-bottom: 5px;margin-bottom: 15px;">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h6>Column Detail:</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="erp-col-form-label col-lg-4">Column Show:</label>
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                                    <input type="checkbox" name="column_toggle" class="column_toggle" checked>
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="erp-col-form-label">Criteria Active:</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                                    <input type="checkbox" name="criteria_active" class="criteria_active" checked>
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="erp-col-form-label col-lg-4">Heading Name:</label>
                                    <input type="text" class="col-lg-8 form-control erp-form-control-sm report_dynamic_heading_name" name="report_dynamic_heading_name">
                                </div>
                                <div class="row">
                                    <label class="erp-col-form-label col-lg-4">Key Name:</label>
                                    <input type="text" class="col-lg-8 form-control erp-form-control-sm report_dynamic_key_name" name="report_dynamic_key_name">
                                </div>
                                <div class="row">
                                    <label class="erp-col-form-label col-lg-4">Column Type:</label>
                                    <div class="col-lg-8 kt-padding-0">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm report_dynamic_column_type" name="report_dynamic_column_type">
                                                <option value="0">Select</option>
                                                @foreach($data['column_types'] as $key=>$column_types)
                                                    <option value="{{$key}}">{{$column_types}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="report_dynamic_decimal_block" style="display: none">
                                    <label class="col-lg-4 erp-col-form-label">Decimal:</label>
                                    <input type="text" class="col-lg-8 form-control erp-form-control-sm validNumber report_dynamic_decimal" name="report_dynamic_decimal">
                                </div>
                                <div class="row" id="report_dynamic_calculation_block" style="display: none">
                                    <label class="col-lg-4 erp-col-form-label">Calculation:</label>
                                    <div class="col-lg-8 kt-checkbox-inline">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                            <input type="checkbox" name="report_dynamic_calculation">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h6>Table Heading:</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Column Align:</label>
                                    <div class="col-lg-6 kt-padding-0">
                                        <div class="row text-center column-align">
                                            <input type="hidden" class="column_align_val" value="left" name="report_dynamic_heading_style_column_align">
                                            <div class="col-lg-4 sel-col-align">
                                                <i class="fa fa-align-left fa-active" data-value="left"></i>
                                            </div>
                                            <div class="col-lg-4 sel-col-align">
                                                <i class="fa fa-align-center" data-value="center"></i>
                                            </div>
                                            <div class="col-lg-4 sel-col-align">
                                                <i class="fa fa-align-right" data-value="right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Font Size:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="text" name="report_dynamic_heading_style_font_size" class="validNumber text-left form-control erp-form-control-sm" value="">
                                        <div class="input-group-append">
                                    <span class="input-group-text erp-form-control-sm">
                                        px
                                    </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Color:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="color" name="report_dynamic_heading_style_color" class="form-control erp-form-control-sm" value="#e2e5ec">
                                        <div class="input-group-append">
                                    <span class="input-group-text erp-form-control-sm">
                                        <label class="kt-checkbox kt-checkbox--single">
                                            <input class="transparent" name="report_dynamic_heading_style_color_transparent" type="checkbox" checked>
                                            <span></span>
                                        </label>
                                    </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Background Color:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="color" name="report_dynamic_heading_style_bgcolor" class="form-control erp-form-control-sm" value="#e2e5ec">
                                        <div class="input-group-append">
                                    <span class="input-group-text erp-form-control-sm">
                                        <label class="kt-checkbox kt-checkbox--single">
                                            <input class="transparent" name="report_dynamic_heading_style_bgcolor_transparent" type="checkbox" checked>
                                            <span></span>
                                        </label>
                                    </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Width:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="text" name="report_dynamic_heading_style_width" class="validNumber text-left form-control erp-form-control-sm" value="">
                                        <div class="input-group-append">
                                    <span class="input-group-text erp-form-control-sm">
                                        px
                                    </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h6>Table Body:</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Column Align:</label>
                                    <div class="col-lg-6 kt-padding-0">
                                        <div class="row text-center column-align">
                                            <input type="hidden" class="column_align_val" value="left" name="report_dynamic_body_style_column_align">
                                            <div class="col-lg-4 sel-col-align">
                                                <i class="fa fa-align-left fa-active" data-value="left"></i>
                                            </div>
                                            <div class="col-lg-4 sel-col-align">
                                                <i class="fa fa-align-center" data-value="center"></i>
                                            </div>
                                            <div class="col-lg-4 sel-col-align">
                                                <i class="fa fa-align-right" data-value="right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Font Size:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="text" name="report_dynamic_body_style_font_size" class="validNumber text-left form-control erp-form-control-sm" value="">
                                        <div class="input-group-append">
                                    <span class="input-group-text erp-form-control-sm">
                                        px
                                    </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Color:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="color" name="report_dynamic_body_style_color" class="form-control erp-form-control-sm" value="#e2e5ec">
                                        <div class="input-group-append">
                                    <span class="input-group-text erp-form-control-sm">
                                        <label class="kt-checkbox kt-checkbox--single">
                                            <input class="transparent" name="report_dynamic_body_style_color_transparent" type="checkbox" checked>
                                            <span></span>
                                        </label>
                                    </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Background Color:</label>
                                    <div class="col-lg-6 kt-padding-0 input-group">
                                        <input type="color" name="report_dynamic_body_style_bgcolor" class="form-control erp-form-control-sm" value="#e2e5ec">
                                        <div class="input-group-append">
                                    <span class="input-group-text erp-form-control-sm">
                                        <label class="kt-checkbox kt-checkbox--single">
                                            <input name="report_dynamic_body_style_bgcolor_transparent" class="transparent" type="checkbox" checked>
                                            <span></span>
                                        </label>
                                    </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-9"></div>
                            <div class="col-lg-3 text-right">
                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger report-filter-del-btn" style="top: 0px;">
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
                        Add New Column
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
