@for($i=0;$i<count($column_toggle);$i++)
    @if($column_toggle[$i] != 0 && $column_keys[$i] != 'branch_name')
        <div class="row form-group-block sub_qry" row-id="{{$i}}">
            <div class="col-lg-3">
                <label class="erp-col-form-label">{{$headings[$i]}}:</label>
                <input type="hidden" value="{{$column_types[$i]}}" name="sub_qry[{{$i}}][key_type]">
                <input type="hidden" value="{{$column_keys[$i]}}" name="sub_qry[{{$i}}][key]">
            </div>
            <div class="col-lg-3">
                <div class="erp-select2">
            @if($column_types[$i] == 'varchar2')
                <select name="sub_qry[{{$i}}][conditions]" class="form-control kt-select2 erp-form-control-sm">
                    <option value="0">Select</option>
                    <option value="like">contains</option>
                    <option value="not like">doesn't contain</option>
                    <option value="=">is equal to</option>
                    <option value="!=">is not equal to</option>
                </select>
            @elseif($column_types[$i] == 'number' || $column_types[$i] == 'float')
                <select name="sub_qry[{{$i}}][conditions]" class="sub_qry_condition form-control kt-select2 erp-form-control-sm">
                    <option value="0">Select</option>
                    <option value="between">between</option>
                    <option value="=">is equal to</option>
                    <option value="!=">is not equal to</option>
                    <option value=">">greater than</option>
                    <option value="<">less than</option>
                    <option value=">=">greater than or equal to</option>
                    <option value="<=">less than or equal to</option>
                </select>
            @elseif($column_types[$i] == 'date')

            @endif
                </div>
            </div>
            <div class="col-lg-3 sub_qry_value_fields">
                @if($column_types[$i] == 'varchar2')
                    @php
                        $id = "none";
                        if($column_keys[$i] == 'store_name'){
                            $id = "store_op";
                        }
                        if($column_keys[$i] == 'display_location_name_string'){
                            $id = "display_location_name_string";
                        }
                    @endphp
                    <div class="erp-select2" id="{{$id}}_multi_select">
                        <select class="form-control erp-form-control-sm {{$id=='none'?'kt_select_none':""}}" id="kt_select_{{$id}}" name="sub_qry[{{$i}}][val]">
                            <option></option>
                        </select>
                    </div>
                    {{--<input type="text" class="form-control erp-form-control-sm" name="sub_qry[{{$i}}][val]">--}}
                @elseif($column_types[$i] == 'date')

                @elseif($column_types[$i] == 'number' || $column_types[$i] == 'float')
                    <input type="text" class="form-control erp-form-control-sm text-right validNumber" name="sub_qry[{{$i}}][val]">
                @endif
            </div>
        </div>
    @endif
@endfor

@if($data['case_name'] == 'vouchers_list')
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Hide:</label>
        </div>
        <div class="col-lg-6">
            <div class="kt-checkbox-inline">
                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                    <input type="checkbox" name="hide_total"> Total
                    <span></span>
                </label>
            </div>
        </div>
    </div>
@endif
@if($data['case_name'] == 'accounting_ledger')
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Opening Balance:</label>
        </div>
        <div class="col-lg-6">
            <div class="kt-checkbox-inline">
                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                    <input type="checkbox" name="accounting_ledger_ob_toggle" checked>
                    <span></span>
                </label>
            </div>
        </div>
    </div>
@endif
