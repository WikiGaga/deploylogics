<div class="row">
    <div class="col-lg-12 text-center">
        <div class="product-barcode-innertabs--title">Sale Rate</div>
    </div>
</div>{{-- end row--}}
<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblR">
    <thead>
    <tr>
        <th width="40%">Branch Name</th>
        @php
            $rate_category_width = 60/(int)count($data['rate_category']);
            $iii = 2;
        @endphp
        @foreach($data['rate_category'] as $key=>$rate_category)
            <th data-id="{{$key+$iii}}" width="{{$rate_category_width}}%"><div style="/*display: inline-block;position: relative;top: 9px;*/">{{$rate_category->rate_category_name}}</div> <input type="text" id="SaleRateApplyAll" value="0" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" style="background: #f0f8ff;/*width: 75px;display: inline-block;float: right;*/"></th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @php $sr = 0; @endphp
    @foreach($data['branch'] as $key=>$branch)
        <tr>
            <td><input type="hidden" class="branch_R" name="rate_branchId_{{$sr}}" value="{{$branch->branch_id}}"><b>{{$branch->branch_name}}</b></td>
            @php $r = 0; @endphp
            @foreach($data['rate_category'] as $rate_category)
                @if($case == $edit || $case == $view)
                    @foreach($pb['sale_rate'] as $saleRate)
                        @if($saleRate->branch_id == $branch->branch_id && $saleRate->product_category_id == $rate_category->rate_category_id)
                            @php $sale_rate_rate = $saleRate->product_barcode_sale_rate_rate @endphp
                            @break
                        @endif
                    @endforeach
                @endif
                <td>
                    <input type="hidden" class="rate_R" id="rate_R_{{$sr}}_{{$r}}" name="rate_categoryId_{{$sr}}_{{$r}}" value="{{$rate_category->rate_category_id}}">
                    @if($branch->branch_id == auth()->user()->branch_id)
                        <input type="text" class="form-control erp-form-control-sm mob_no validNumber validOnlyFloatNumber sale_rate_rate" id="sale_cb_{{$r}}" value="{{isset($sale_rate_rate)? number_format($sale_rate_rate,3):""}}" name="rate_categoryVal_{{$sr}}_{{$r}}">
                    @else
                        <input type="text" class="form-control erp-form-control-sm mob_no validNumber validOnlyFloatNumber sale_rate_rate" value="{{isset($sale_rate_rate)? number_format($sale_rate_rate,3):""}}" name="rate_categoryVal_{{$sr}}_{{$r}}">
                    @endif
                </td>
                @php $r++; @endphp
            @endforeach
        </tr>
        @php $sr++; @endphp
    @endforeach
    </tbody>
</table>

<div class="row">
    <div class="col-lg-12 text-center">
        <div class="product-barcode-innertabs--title">Purchase Rate</div>
    </div>
</div>{{-- end row--}}
<div class="row">
    <div class="col-lg-12">
        <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblPurcRate">
            <thead>
            <tr>
                <th width="40%">Branch Name</th>
                <th width="20%"><div style="/*display: inline-block;position: relative;top: 9px;*/">Purchase Rate</div> <input type="text" id="PurcRateApplyAll" value="0" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" style="background: #f0f8ff;/*width: 75px;display: inline-block;float: right;*/"></th>
                <th width="20%"><div style="/*display: inline-block;position: relative;top: 9px;*/">Cost Rate</div> <input type="text" id="CostRateApplyAll" value="0" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" style="background: #f0f8ff;/*width: 75px;display: inline-block;float: right;*/"></th>
                <th width="20%"><div style="/*display: inline-block;position: relative;top: 9px;*/">Avg Rate</div> <input type="text" id="AvgRateApplyAll" value="0" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" style="background: #f0f8ff;/*width: 75px;display: inline-block;float: right;*/"></th>
            </tr>
            </thead>
            <tbody>
            @php $pr = 0; @endphp
            @foreach($data['branch'] as $key=>$branch)
                @if($case == $edit || $case == $view)
                    @foreach($pb['purc_rate'] as $purc_rate)
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
                    @endforeach
                @endif
                <tr class={{auth()->user()->branch_id == $branch->branch_id ?"current_branch_purc_rate":""}}>
                    <td>
                        <input type="hidden" class="branch_PR" name="pr_branch_id_{{$pr}}" value="{{$branch->branch_id}}">
                        <b>{{$branch->branch_name}}</b>
                    </td>
                    <td>
                        <input type="text" value="{{isset($purchase_rate) ? number_format($purchase_rate,3) :""}}" class="purchase_rate form-control erp-form-control-sm validNumber validOnlyFloatNumber" maxlength="15" name="pr_purchase_value_{{$pr}}">
                    </td>
                    <td>
                        <input type="text" value="{{isset($cost_rate) ? number_format($cost_rate,3) : ""}}" class="cost_rate form-control erp-form-control-sm validNumber validOnlyFloatNumber" maxlength="15" name="pr_cost_value_{{$pr}}">
                    </td>
                    <td>
                        <input type="text" value="{{isset($avg_rate) ? number_format($avg_rate,3) : ""}}" class="avg_rate form-control erp-form-control-sm validNumber validOnlyFloatNumber" maxlength="15" name="pr_avg_value_{{$pr}}">
                    </td>
                </tr>
                @php $pr++; @endphp
            @endforeach
            </tbody>
        </table>
    </div>
</div>
