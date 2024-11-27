<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblT">
    <thead>
    <tr>
        <th width="55%">Branch Name</th>
        <th width="30%"><div style="display: inline-block;position: relative;top: 9px;">Tax Value</div>  <input type="text" id="TaxValueApplyAll" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" style="width: 100px;display: inline-block;float: right;"></th>
        <th width="15%">
            <div class="kt-checkbox-inline">
                <div style="display: inline-block;">Apply Tax</div>
                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" style="float: right">
                    <input type="checkbox" id="TaxStatusApplyAll">
                    <span></span>
                </label>
            </div>
        </th>
    </tr>
    </thead>
    <tbody>
    @php $sr = 0; @endphp
    @foreach($data['branch'] as $key=>$branch)
        @if($case == $edit || $case == $view)
            @foreach($pb['barcode_dtl'] as $b_dtl)
                @php
                    $product_barcode_tax_value =  "";
                    $product_barcode_tax_apply =  "";
                @endphp
                @if($b_dtl->branch_id == $branch->branch_id)
                    @php
                        $product_barcode_tax_value =  $b_dtl->product_barcode_tax_value;
                        $product_barcode_tax_apply =  isset($b_dtl->product_barcode_tax_apply)?$b_dtl->product_barcode_tax_apply:0;
                    @endphp
                    @break
                @endif
            @endforeach
        @endif
        @php
            $tax_apply =  isset($product_barcode_tax_apply)?$product_barcode_tax_apply:0;
        @endphp
        <tr>
            <td><input type="hidden" class="branch_T" name="tax_branch_id_{{$sr}}" value="{{$branch->branch_id}}"><b>{{$branch->branch_name}}</b></td>

            <td>
                <input type="text" value="{{isset($product_barcode_tax_value)?$product_barcode_tax_value:""}}" class="form-control erp-form-control-sm tax_value mob_no validNumber validOnlyFloatNumber" name="tax_tax_value_{{$sr}}">
            </td>
            <td>
                <span class="kt-switch kt-switch--sm kt-switch--icon">
                    <label>
                        <input type="checkbox" class="tax_status" name="tax_tax_status_{{$sr}}" {{$tax_apply==1?"checked":""}}>
                        <span></span>
                    </label>
                </span>
            </td>
        </tr>
        @php $sr++; @endphp
    @endforeach
    </tbody>
</table>
