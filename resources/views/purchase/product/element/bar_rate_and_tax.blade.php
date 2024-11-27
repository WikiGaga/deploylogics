@php $sr = 0; @endphp
<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblR">
    <thead>
        <tr>
            <th width="9%">Branch Name</th>
            <th width="9%">Net TP</th>
            <th width="9%">Sale Rate</th>
            <th width="9%">Whole Sale Rate</th>
            <th width="9%">
                Tax Group
                <select class="form-control erp-form-control-sm tax_group_block">
                    <option data-id="0" value="0">Select</option>
                    @foreach($data['tax_group'] as $tax_group )
                        <option data-id="{{$tax_group->tax_group_value}}" value="{{$tax_group->tax_group_id}}" >{{$tax_group->tax_group_name}}</option>
                    @endforeach
                </select>
            </th>
            <th width="9%">Tax Rate</th>
            <th width="9%">
                GST Calculation
                <select class="form-control erp-form-control-sm gst_calculation_block">
                    <option value="0">Select</option>
                    @foreach($data['gst_clac'] as $gst_clac )
                        <option value="{{$gst_clac->gst_calculation_id}}">{{$gst_clac->gst_calculation_name}}</option>
                    @endforeach
                </select>
            </th>
            <th width="9%">Inc. Tax Price</th>
            <th width="9%">GP %</th>
            <th width="9%">GP Amount</th>
            <th width="9%">
                HS Code
                <input type="text" class="form-control erp-form-control-sm hs_code_block">
            </th>
        </tr>
    </thead>
    <tbody>
    @foreach($data['branch'] as $key=>$branch)
        @php
        // dd($branch->branch_id);
            $hs_code = "";
            $cost_rate = "";
            $sale_rate = "";
            $tax_value = "";
            $inclusive_tax_price = "";
            $gp_perc = "";
            $gp_amount = "";
            $whole_sale_rate = "";
            $tax_group_id = "";
            $gst_calculation_id = "";
            
        @endphp
        @if($case == $edit || $case == $view)
        @foreach($pb['purc_rate'] as $purc_rate)
            @if($purc_rate->branch_id == $branch->branch_id)
                @php
                    $hs_code = $purc_rate->hs_code;
                    $cost_rate = $purc_rate->net_tp; //product_barcode_cost_rate;
                    $sale_rate = $purc_rate->sale_rate;
                    $whole_sale_rate = $purc_rate->whole_sale_rate;
                    $tax_group_id = $purc_rate->tax_group_id;
                    //$tax_value = $purc_rate->tax_rate;
                    $tax_value = $purc_rate->sale_tax_rate;
                    $inclusive_tax_price = $purc_rate->inclusive_tax_price;
                    $gst_calculation_id = $purc_rate->gst_calculation_id;
                    $gp_perc = $purc_rate->gp_perc;
                    $gp_amount = $purc_rate->gp_amount;
                @endphp
            @endif
        @endforeach
        @endif
        <tr>
            <td><input type="hidden" class="branch_R" name="rate_branchId_{{$sr}}" value="{{$branch->branch_id}}"><b>{{$branch->branch_name}}</b></td>
            <td>
                <input type="text" value="{{$cost_rate}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber cost_rate" name="cost_rate_{{$sr}}">
            </td>
            <td>
                <input type="text" value="{{$sale_rate}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber sale_rate" name="sale_rate_{{$sr}}">
            </td>
            <td>
                <input type="text" value="{{$whole_sale_rate}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber whole_sale_rate" name="whole_sale_rate_{{$sr}}">
            </td>
            <td>
                <select class="form-control erp-form-control-sm tax_group" name="tax_group_id_{{$sr}}">
                    <option data-id="0" value="0">Select</option>
                    @foreach($data['tax_group'] as $tax_group )
                        <option data-id="{{$tax_group->tax_group_value}}" value="{{$tax_group->tax_group_id}}" {{$tax_group->tax_group_id==$tax_group_id?'selected':''}}>{{$tax_group->tax_group_name}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" value="{{$tax_value}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber readonly tax_value" name="tax_value_{{$sr}}" readonly>
            </td>
            <td>
                <select class="form-control erp-form-control-sm gst_calculation_id" name="gst_calculation_id_{{$sr}}">
                    <option data-id="0" value="0">Select</option>
                    @foreach($data['gst_clac'] as $gst_clac )
                        <option value="{{$gst_clac->gst_calculation_id}}" {{$gst_clac->gst_calculation_id==$gst_calculation_id?'selected':''}}>{{$gst_clac->gst_calculation_name}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" value="{{$inclusive_tax_price}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber inclusive_tax_price readonly" name="inclusive_tax_price_{{$sr}}" readonly>
            </td>
            <td>
                <input type="text" value="{{$gp_perc}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber gp_perc readonly" name="gp_perc_{{$sr}}" readonly>
            </td>
            <td>
                <input type="text" value="{{$gp_amount}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber gp_amount readonly" name="gp_amount_{{$sr}}" readonly>
            </td>
            <td>
                <input type="text" value="{{$hs_code}}" class="form-control erp-form-control-sm hs_code" name="hs_code_{{$sr}}">
            </td>
        </tr>
        @php $sr += 1; @endphp
    @endforeach
    </tbody>
</table>
