<style>
    div#erp_row__summary_fixed.fixed{
        position: fixed;
        right: 10px;
        top: 0;
        z-index: 999;
        margin-top: 0 !important;
    }
    table.erp_table__summary {
        border: 1px solid #ebedf2;
    }
    table.erp_table__summary>.erp_table__summary_header th {
        vertical-align: bottom;
        border-bottom: 2px solid #ebedf2;
        border-bottom-width: 1px;
        position: -webkit-sticky;
        position: sticky;
        top: -1px;
        font-size: 11px;
        font-weight: 500 !important;
        padding: 0 !important;
        font-family: Roboto;
        border-right: 1px solid #ebedf2;
        z-index: 3;
        background: #443d36 !important;
        border-top: 0;
        color: #fff;
    }
    thead.erp_table__summary_header th input:read-only {
        background: #443d36 !important;
        color: #fff;
    }
</style>
<div id="erp_row__summary_fixed" class="row form-group-block">
    <div class="col-lg-12">
        <table class="table erp_table__summary dtr-inline">
            <thead class="erp_table__summary_header">
            <tr>
                <th scope="col">
                    <div class="erp_form__grid_th_title">
                        <input type="checkbox" id="table__summary_fixed">
                        Total Item
                    </div>
                    <div class="erp_form__grid_th_input">
                        <input value="0" type="text" name="summary_total_item" class="summary_total_item validNumber validOnlyNumber form-control erp-form-control-sm" readonly>
                    </div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Qty</div>
                    <div class="erp_form__grid_th_input">
                        <input value="0" type="text" id="summary_qty_wt" name="summary_qty_wt" class="summary_qty_wt validNumber validOnlyNumber form-control erp-form-control-sm" readonly style="font-weight: 800;">
                    </div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Amount</div>
                    <div class="erp_form__grid_th_input">
                        <input value="0" type="text" id="summary_net_amount" name="summary_net_amount" class="summary_net_amount validNumber validOnlyNumber form-control erp-form-control-sm" readonly style="font-weight: 800;">
                    </div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Adv. Tax %</div>
                    <div class="erp_form__grid_th_input">
                        <input value="{{ isset($data['current']->stock_advance_tax_perc) ? $data['current']->stock_advance_tax_perc : '' }}" type="text" id="overall_vat_perc" name="overall_vat_perc" class="overall_vat_perc validNumber validOnlyNumber form-control erp-form-control-sm" >
                    </div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Adv. Tax Amt</div>
                    <div class="erp_form__grid_th_input">
                        <input value="{{ isset($data['current']->stock_advance_tax_amount) ? $data['current']->stock_advance_tax_amount : '' }}" type="text" id="overall_vat_amount" name="overall_vat_amount" class="overall_vat_amount validNumber validOnlyNumber form-control erp-form-control-sm" >
                    </div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Total Amount</div>
                    <div class="erp_form__grid_th_input">
                        <input value="0" type="text" id="overall_net_amount" name="overall_net_amount" class="overall_net_amount validNumber validOnlyNumber form-control erp-form-control-sm" readonly style="font-weight: 800;">
                    </div>
                </th>
            </tr>
            </thead>
        </table>
    </div>
</div>

@section('summary_total_pageJS')
    <script>
        $(document).on('keyup blur','#overall_vat_perc,#overall_vat_amount',function(e){
            var current_id = $(this).attr('id');
            var arr = {
                current_id : current_id
            }
            getValuesForDisc(arr);
        });
        function getValuesForDisc(arr = {}){
            var current_id = !valueEmpty(arr.current_id)?arr.current_id:"";
            var data = {}
            data.summary_net_amount = $(document).find('#summary_net_amount').val();
            data.overall_vat_perc = $(document).find('#overall_vat_perc').val();
            data.overall_vat_amount = $(document).find('#overall_vat_amount').val();
            if(valueEmpty(data.summary_net_amount)){
                data.summary_net_amount = 0;
            }
            if(valueEmpty(data.overall_vat_perc)){
                data.overall_vat_perc = 0;
            }
            if(valueEmpty(data.overall_vat_amount)){
                data.overall_vat_amount = 0;
            }
            if(parseFloat(data.summary_net_amount) != 0) {
                
                var calc_vat = false;
                if(current_id == 'overall_vat_perc' || ( valueEmpty(current_id) && !valueEmpty(data.overall_vat_perc)))
                {
                    var value = (parseFloat(data.summary_net_amount) * parseFloat(data.overall_vat_perc)) / 100;
                    if(data.overall_vat_perc > 100){
                        value = 0;
                        $(document).find('#overall_vat_perc').val(value.toFixed(3));
                    }
                    $(document).find('#overall_vat_amount').val(value.toFixed(3));
                    calc_vat = true
                }
                if(current_id == 'overall_vat_amount' || ( valueEmpty(current_id) && calc_vat == false))
                {
                    var value = (parseFloat(data.overall_vat_amount) / parseFloat(data.summary_net_amount)) * 100;
                    if(value > 100){
                        value = 0;
                        $(document).find('#overall_vat_amount').val(value.toFixed(3));
                    }
                    $(document).find('#overall_vat_perc').val(value.toFixed(3));
                }
                funcGetOverallNetAmount()
            }
        }
        function funcGetOverallNetAmount(){
            var summary_net_amount = $(document).find('#summary_net_amount').val();
            var overall_vat_amount = $(document).find('#overall_vat_amount').val();
            if(valueEmpty(summary_net_amount)){
                var summary_net_amount = 0;
            }
            if(valueEmpty(overall_vat_amount)){
                var overall_vat_amount = 0;
            }
            var overall_net_amount =  parseFloat(summary_net_amount) + parseFloat(overall_vat_amount);

            if(valueEmpty(overall_net_amount)){
                var overall_net_amount = 0;
            }
            //$(document).find('.overall_net_amount').val(Math.round(overall_net_amount));
            $(document).find('.overall_net_amount').val(overall_net_amount.toFixed(3));
        }
        funcGetOverallNetAmount();

        $(document).on('click','#table__summary_fixed',function(){
            if($(this).prop('checked')){
                $('#erp_row__summary_fixed').addClass('fixed')
                var w = parseFloat($('.kt-container').width());
                $('#erp_row__summary_fixed').css({'width':w+'px'})
            }else{
                $('#erp_row__summary_fixed').removeClass('fixed')
                $('#erp_row__summary_fixed').css({'width':''})
            }
        });
    </script>

@endsection
