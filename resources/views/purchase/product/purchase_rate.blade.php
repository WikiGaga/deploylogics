@php
    if(isset($data['current']->product_id)){
        $purc_rates = $pb['purc_rate'];
    }else{
        $purc_rates = [];
    }
@endphp
<div class="row">
    <div class="col-lg-12">
        <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblPurcRate">
            <thead>
            <tr>
                <th width="25%">Branch Name</th>
                <th width="25%"><div style="/*display: inline-block;position: relative;top: 9px;*/">Purchase Rate</div> <input type="text" id="PurcRateApplyAll" value="0" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" style="background: #f0f8ff;/*width: 75px;display: inline-block;float: right;*/"></th>
                <th width="25%"><div style="/*display: inline-block;position: relative;top: 9px;*/">Cost Rate</div> <input type="text" id="CostRateApplyAll" value="0" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" style="background: #f0f8ff;/*width: 75px;display: inline-block;float: right;*/"></th>
                <th width="25%"><div style="/*display: inline-block;position: relative;top: 9px;*/">Avg Rate</div> <input type="text" id="AvgRateApplyAll" value="0" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" style="background: #f0f8ff;/*width: 75px;display: inline-block;float: right;*/"></th>
            </tr>
            </thead>
            <tbody>
            @php $pr = 0; @endphp
            @foreach($data['branch'] as $key=>$branch)
                @foreach($purc_rates as $purc_rate)
                    @if($purc_rate->branch_id == $branch->branch_id)
                        @php
                            if($purc_rate->product_barcode_purchase_rate != null && $purc_rate->product_barcode_purchase_rate != ""){
                                $purchase_rate = $purc_rate->product_barcode_purchase_rate;
                            }
                            if($purc_rate->product_barcode_cost_rate != null && $purc_rate->product_barcode_cost_rate != ""){
                                $cost_rate = $purc_rate->product_barcode_cost_rate;
                            }
                            if($purc_rate->product_barcode_avg_rate != null && $purc_rate->product_barcode_avg_rate != ""){
                                $avg_rate = $purc_rate->product_barcode_avg_rate;
                            }
                        @endphp
                        @break
                    @endif
                    @php
                        $purchase_rate = "";
                        $cost_rate = "";
                        $avg_rate = "";
                    @endphp
                @endforeach
                <tr class={{auth()->user()->branch_id == $branch->branch_id ?"current_branch_purc_rate":""}}>
                    <td>
                        <input type="hidden" class="branch_PR" name="pr_branch_id_{{$pr}}" value="{{$branch->branch_id}}">
                        <b>{{$branch->branch_name}}</b>
                    </td>
                    <td>
                        <input type="text" value="{{isset($purchase_rate) && $purchase_rate != "" ? number_format($purchase_rate,3) :""}}" class="purchase_rate form-control erp-form-control-sm validNumber validOnlyFloatNumber" maxlength="15" name="pr_purchase_value_{{$pr}}">
                    </td>
                    <td>
                        <input type="text" value="{{isset($cost_rate) && $cost_rate != "" ? number_format($cost_rate,3) : ""}}" class="cost_rate form-control erp-form-control-sm validNumber validOnlyFloatNumber" maxlength="15" name="pr_cost_value_{{$pr}}">
                    </td>
                    <td>
                        <input type="text" value="{{isset($avg_rate) && $avg_rate != "" ? number_format($avg_rate,3) : ""}}" class="avg_rate form-control erp-form-control-sm validNumber validOnlyFloatNumber" maxlength="15" name="pr_avg_value_{{$pr}}">
                    </td>
                </tr>
                @php $pr++; @endphp
            @endforeach
            </tbody>
        </table>
    </div>
</div>
