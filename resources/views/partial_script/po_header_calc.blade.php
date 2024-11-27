{{-- call on (PO,GRN,PR) forms  --}}
<script>
    function funcNumberFloat(num,fixed=4){
        var number = num;
        if(valueEmpty(number)){
            return parseFloat(0).toFixed(fixed);
        }
        if(typeof num == 'string'){
            number = num.toString();
            number = number.replaceAll(',', '')
        }
        if(valueEmpty(fixed)) {
            return parseFloat(number);
        }else{
            return parseFloat(number).toFixed(fixed);
        }
    }
    function funcCalcNumberFloat(num,fixed=4){
        var number = num;
        if(valueEmpty(number)){
            return parseFloat(0);
        }
        if(typeof num == 'string'){
            number = num.toString();
            number = number.replaceAll(',', '')
        }
        return parseFloat(number);
    }
    function funcNumValid(num){
        if(!valueEmpty(num)){
            var str = num.toString();
            return parseFloat(str.replaceAll(',', ''))
        }
        return 0;
    }

    $(document).on('change', '.pd_tax_on,.pd_disc',function(){
        var thix = this;
        var tr = $(this).parents('tr');
        funcHeaderCalc(tr,thix);
    });

    $(document).on('keyup blur' , '.tblGridCal_qty,.tblGridCal_rate,.tblGridCal_sale_rate,.tblGridCal_mrp,.tblGridCal_discount_perc,.tblGridCal_discount_amount,.tblGridCal_gst_perc,.tblGridCal_gst_amount,.tblGridCal_fed_perc,.tblGridCal_fed_amount,.tblGridCal_spec_disc_perc,.tblGridCal_spec_disc_amount',function(e){
        //Cost according to quantity
        var thix = this;
        var tr = $(this).parents('tr');
        funcHeaderCalc(tr,thix);
    });

    function funcHeaderCalc(tr,thix = null){
        var cListArr = [];
        if(!valueEmpty(thix)){
            var cList = thix.classList;
            for(var i=0;i<cList.length;i++){
                cListArr.push(cList[i]);
            }
        }
        //debugger
        var qty = tr.find('.tblGridCal_qty').val();
        var rate = tr.find('.tblGridCal_rate').val();

        var calc_amount = funcCalcNumberFloat(qty) * funcCalcNumberFloat(rate);
        tr.find('.tblGridCal_cost_amount').val(funcNumberFloat(calc_amount));
        var sale_rate = tr.find('.tblGridCal_sale_rate').val();
        var mrp = tr.find('.tblGridCal_mrp').val();

        if(cListArr.includes('tblGridCal_discount_amount')){
            var calc_discount_amount = tr.find('.tblGridCal_discount_amount').val();
            var calc_discount_perc = (funcCalcNumberFloat(calc_discount_amount) / funcCalcNumberFloat(calc_amount)) * 100;
            if(calc_discount_perc > 100){
               // tr.find('.tblGridCal_discount_amount').val(0)
                calc_discount_perc = 0;
            }
            tr.find('.tblGridCal_discount_perc').val(funcNumberFloat(calc_discount_perc));
        }else{
            var discount_perc = tr.find('.tblGridCal_discount_perc').val();
            var calc_discount_amount = funcCalcNumberFloat(calc_amount) * funcCalcNumberFloat(discount_perc) / 100;
            if(discount_perc > 100){
                tr.find('.tblGridCal_discount_perc').val(0)
                calc_discount_amount = 0;
            }
            tr.find('.tblGridCal_discount_amount').val(funcNumberFloat(calc_discount_amount));
        }

        var calc_after_discount_amount = funcCalcNumberFloat(calc_amount) - funcCalcNumberFloat(calc_discount_amount);
        tr.find('.tblGridCal_after_discount_amount').val(funcNumberFloat(calc_after_discount_amount));

        var pd_tax_on = tr.find('.pd_tax_on option:selected').val();

        if(valueEmpty(pd_tax_on)){
            pd_tax_on = 'da';
        }

        if(cListArr.includes('tblGridCal_gst_amount')){
            var calc_gst_amount = tr.find('.tblGridCal_gst_amount').val();
            var gst_perc = 0;
            if(pd_tax_on.toLowerCase() == 'mrp'){
                //var gst_perc_include = funcCalcNumberFloat(mrp) - funcCalcNumberFloat(calc_gst_amount);
                var gst_perc_include = (funcCalcNumberFloat(mrp) * funcCalcNumberFloat(qty)) - funcCalcNumberFloat(calc_gst_amount);
                gst_perc = (funcCalcNumberFloat(calc_gst_amount) / funcCalcNumberFloat(gst_perc_include)) * 100;
            }else{ //pd_tax_on = 'da'
                gst_perc = (funcCalcNumberFloat(calc_gst_amount) / funcCalcNumberFloat(calc_after_discount_amount) ) * 100;
            }
            if(gst_perc > 100){
                //tr.find('.tblGridCal_gst_amount').val(0)
                gst_perc = 0;
            }
            tr.find('.tblGridCal_gst_perc').val(funcNumberFloat(gst_perc));

        }else{
            var gst_perc = tr.find('.tblGridCal_gst_perc').val();
            var calc_gst_amount = 0;
            if(pd_tax_on.toLowerCase() == 'mrp'){
                var gst_perc_include = funcCalcNumberFloat(100) + funcCalcNumberFloat(gst_perc);
                calc_gst_amount = (funcCalcNumberFloat(mrp) * funcCalcNumberFloat(qty)) * funcCalcNumberFloat(gst_perc) / funcCalcNumberFloat(gst_perc_include);
            }else{ //pd_tax_on = 'da'
                calc_gst_amount = funcCalcNumberFloat(calc_after_discount_amount) * funcCalcNumberFloat(gst_perc) / 100;
            }
            if(gst_perc > 100){
                tr.find('.tblGridCal_gst_perc').val(0);
                calc_gst_amount = 0;
            }
            tr.find('.tblGridCal_gst_amount').val(funcNumberFloat(calc_gst_amount));
        }


        if(cListArr.includes('tblGridCal_fed_amount')){
            var calc_fed_amount = tr.find('.tblGridCal_fed_amount').val();
            var fed_perc = 0;
            if(pd_tax_on.toLowerCase() == 'mrp'){
               // var fed_perc_include = funcCalcNumberFloat(mrp) - funcCalcNumberFloat(calc_fed_amount);
               // fed_perc = 100 * funcCalcNumberFloat(calc_fed_amount) / funcCalcNumberFloat(fed_perc_include);
               var fed_perc_include = (funcCalcNumberFloat(mrp) * funcCalcNumberFloat(qty)) - funcCalcNumberFloat(calc_fed_amount);
               fed_perc = (funcCalcNumberFloat(calc_fed_amount) / funcCalcNumberFloat(fed_perc_include)) * 100;
            
            }else{ //pd_tax_on = 'da'
                fed_perc = (funcCalcNumberFloat(calc_fed_amount) / funcCalcNumberFloat(calc_after_discount_amount)) * 100;
            }
            if(fed_perc > 100){
               // tr.find('.tblGridCal_fed_amount').val(0)
                fed_perc = 0;
            }
            tr.find('.tblGridCal_fed_perc').val(funcNumberFloat(fed_perc));

        }else{
            var fed_perc = tr.find('.tblGridCal_fed_perc').val();
            var calc_fed_amount = 0;
            if(pd_tax_on.toLowerCase() == 'mrp'){
                var fed_perc_include = funcCalcNumberFloat(100) + funcCalcNumberFloat(fed_perc);
                calc_fed_amount = (funcCalcNumberFloat(mrp) * funcCalcNumberFloat(qty)) * funcCalcNumberFloat(fed_perc) / funcCalcNumberFloat(fed_perc_include);
            }else{ //pd_tax_on = 'da'
                calc_fed_amount = funcCalcNumberFloat(calc_after_discount_amount) * funcCalcNumberFloat(fed_perc) / 100;
            }
            if(fed_perc > 100){
                tr.find('.tblGridCal_fed_perc').val(0);
                calc_fed_amount = 0;
            }
            tr.find('.tblGridCal_fed_amount').val(funcNumberFloat(calc_fed_amount));
        }

        var pd_disc = tr.find('.pd_disc option:selected').val();
        if(valueEmpty(pd_disc)){
            pd_disc = 'ga';
        }
        if(cListArr.includes('tblGridCal_spec_disc_amount')){
            var calc_spec_disc_amount = tr.find('.tblGridCal_spec_disc_amount').val();
            var spec_disc_perc = 0;
            if(pd_disc.toLowerCase() == 'ga'){
                spec_disc_perc = (funcCalcNumberFloat(calc_spec_disc_amount) / funcCalcNumberFloat(calc_after_discount_amount)) * 100;
            }else if(pd_disc.toLowerCase() == 'ta'){
                spec_disc_perc = (funcCalcNumberFloat(calc_spec_disc_amount) / ( funcCalcNumberFloat(calc_after_discount_amount) + funcCalcNumberFloat(calc_gst_amount) + funcCalcNumberFloat(calc_fed_amount) ) ) * 100;
            }else{ // pd_disc = 'ia'
                spec_disc_perc = (funcCalcNumberFloat(calc_spec_disc_amount) / funcCalcNumberFloat(calc_amount)) * 100;
            }
            if(spec_disc_perc > 100){
                //tr.find('.tblGridCal_spec_disc_amount').val(0)
                spec_disc_perc = 0;
            }
            tr.find('.tblGridCal_spec_disc_perc').val(funcNumberFloat(spec_disc_perc));
        }else{
            var spec_disc_perc = tr.find('.tblGridCal_spec_disc_perc').val();
            var calc_spec_disc_amount = 0;
            if(pd_disc.toLowerCase() == 'ga'){
                calc_spec_disc_amount = funcCalcNumberFloat(calc_after_discount_amount) * funcCalcNumberFloat(spec_disc_perc) / 100;
            }else if(pd_disc.toLowerCase() == 'ta'){
                calc_spec_disc_amount = (funcCalcNumberFloat(calc_after_discount_amount) + funcCalcNumberFloat(calc_gst_amount) + funcCalcNumberFloat(calc_fed_amount)) * funcCalcNumberFloat(spec_disc_perc) / 100;
            }else{ // pd_disc = 'ia'
                calc_spec_disc_amount = funcCalcNumberFloat(calc_amount) * funcCalcNumberFloat(spec_disc_perc) / 100;
            }
            if(spec_disc_perc > 100){
                tr.find('.tblGridCal_spec_disc_perc').val(0);
                calc_spec_disc_amount = 0;
            }
            tr.find('.tblGridCal_spec_disc_amount').val(funcNumberFloat(calc_spec_disc_amount));
        }

        var calc_gross_amount = funcCalcNumberFloat(calc_after_discount_amount) + funcCalcNumberFloat(calc_gst_amount);
        tr.find('.tblGridCal_gross_amount').val(funcNumberFloat(calc_gross_amount))

        var calc_net_amount = funcCalcNumberFloat(calc_gross_amount) + funcCalcNumberFloat(calc_fed_amount) - funcCalcNumberFloat(calc_spec_disc_amount);
        tr.find('.tblGridCal_amount').val(funcNumberFloat(calc_net_amount))

        var calc_net_tp = funcCalcNumberFloat(calc_net_amount) / funcCalcNumberFloat(qty) ;
        if(valueEmpty(calc_net_tp)){
            calc_net_tp = 0;
        }
        tr.find('.tblGridCal_net_tp').val(funcNumberFloat(calc_net_tp));

        var last_tp = tr.find('.tblGridCal_last_tp').val();
        var calc_tp_diff = funcCalcNumberFloat(calc_net_tp) - funcCalcNumberFloat(last_tp);
        tr.find('.tblGridCal_tp_diff').val(funcNumberFloat(calc_tp_diff));

        var calc_gp_amount = funcCalcNumberFloat(sale_rate) - funcCalcNumberFloat(calc_net_tp);
        tr.find('.tblGridCal_gp_amount').val(funcNumberFloat(calc_gp_amount));

        var calc_gp_perc = (funcCalcNumberFloat(calc_gp_amount) / funcCalcNumberFloat(calc_net_tp)) * 100;
        if(valueEmpty(calc_gp_perc)){
            calc_gp_perc = 0;
        }
        tr.find('.tblGridCal_gp_perc').val(funcNumberFloat(calc_gp_perc));

        if (typeof allGridTotal !== 'undefined'){ // func make on GRN form
            allGridTotal();
        }
    }

    function allGridTotal(){
        var t_qty = 0;
        var t_pu = 0;
        var t_per = 0;
        var t_cost = 0;
        var t_sys_qty = 0;
        var t_fc_rate = 0;
        var t_rate = 0;
        var t_sale_rate = 0;
        var t_mrp = 0;
        var t_disc_perc = 0;
        var t_disc_amount = 0;
        var t_after_disc_amount = 0;
        var t_gst_perc = 0;
        var t_gst_amount = 0;
        var t_fed_perc = 0;
        var t_fed_amount = 0;
        var t_spec_disc_perc = 0;
        var t_spec_disc_amount = 0;
        var t_cost_amount = 0;
        var t_gross_amount = 0;
        var t_amount = 0;
        var t_net_tp = 0;
        var t_last_tp = 0;
        var t_vend_last_tp = 0;
        var t_tp_diff = 0;
        var t_gp_perc = 0;
        var t_gp_amount = 0;
        var t_vat_amount = 0;
        var total_item = 0;
        $('.erp_form__grid_body>tr').each(function(){
            total_item += 1;
            var thix = $(this);
            if(funcNumValid(thix.find('.tblGridCal_qty').val())){
                t_qty += funcNumValid(thix.find('.tblGridCal_qty').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_purchase_unit').val())){
                t_pu += funcNumValid(thix.find('.tblGridCal_purchase_unit').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_percentage').val())){
                t_per += funcNumValid(thix.find('.tblGridCal_percentage').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_cost_price').val())){
                t_cost += funcNumValid(thix.find('.tblGridCal_cost_price').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_sys_qty').val())){
                t_sys_qty += funcNumValid(thix.find('.tblGridCal_sys_qty').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_fc_rate').val())){
                t_fc_rate += funcNumValid(thix.find('.tblGridCal_fc_rate').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_rate').val())){
                t_rate += funcNumValid(thix.find('.tblGridCal_rate').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_sale_rate').val())){
                t_sale_rate += funcNumValid(thix.find('.tblGridCal_sale_rate').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_mrp').val())){
                t_mrp += funcNumValid(thix.find('.tblGridCal_mrp').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_discount_perc').val())){
                t_disc_perc += funcNumValid(thix.find('.tblGridCal_discount_perc').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_discount_amount').val())){
                t_disc_amount += funcNumValid(thix.find('.tblGridCal_discount_amount').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_after_discount_amount').val())){
                t_after_disc_amount += funcNumValid(thix.find('.tblGridCal_after_discount_amount').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_gst_perc').val())){
                t_gst_perc += funcNumValid(thix.find('.tblGridCal_gst_perc').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_gst_amount').val())){
                t_gst_amount += funcNumValid(thix.find('.tblGridCal_gst_amount').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_fed_perc').val())){
                t_fed_perc += funcNumValid(thix.find('.tblGridCal_fed_perc').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_fed_amount').val())){
                t_fed_amount += funcNumValid(thix.find('.tblGridCal_fed_amount').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_spec_disc_perc').val())){
                t_spec_disc_perc += funcNumValid(thix.find('.tblGridCal_spec_disc_perc').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_spec_disc_amount').val())){
                t_spec_disc_amount += funcNumValid(thix.find('.tblGridCal_spec_disc_amount').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_cost_amount').val())){
                t_cost_amount += funcNumValid(thix.find('.tblGridCal_cost_amount').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_gross_amount').val())){
                t_gross_amount += funcNumValid(thix.find('.tblGridCal_gross_amount').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_amount').val())){
                t_amount += funcNumValid(thix.find('.tblGridCal_amount').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_net_tp').val())){
                t_net_tp += funcNumValid(thix.find('.tblGridCal_net_tp').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_last_tp').val())){
                t_last_tp += funcNumValid(thix.find('.tblGridCal_last_tp').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_vend_last_tp').val())){
                t_vend_last_tp += funcNumValid(thix.find('.tblGridCal_vend_last_tp').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_tp_diff').val())){
                t_tp_diff += funcNumValid(thix.find('.tblGridCal_tp_diff').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_gp_perc').val())){
                t_gp_perc += funcNumValid(thix.find('.tblGridCal_gp_perc').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_gp_amount').val())){
                t_gp_amount += funcNumValid(thix.find('.tblGridCal_gp_amount').val());
            }
            if(funcNumValid(thix.find('.tblGridCal_vat_amount').val())){
                t_vat_amount += funcNumValid(thix.find('.tblGridCal_vat_amount').val());
            }
        });
        var tr = $('.erp_form__grid_body_total>tr:first-child');
        tr.find('.total_item>input').val(total_item);
        tr.find('.total_grid_qty>input').val(funcNumValid(t_qty));
        tr.find('.total_grid_pu>input').val("") // val(funcNumValid(t_pu).toFixed(3));
        tr.find('.total_grid_sale_rate>input').val("") //.val(funcNumValid(t_sale_rate).toFixed(3));
        tr.find('.total_grid_sys_qty>input').val(funcNumValid(t_sys_qty).toFixed(3));
        tr.find('.total_grid_mrp>input').val("") // val(funcNumValid(t_mrp).toFixed(3));
        tr.find('.total_grid_cost_amount>input').val(funcNumValid(t_cost_amount).toFixed(3));
        tr.find('.total_grid_disc_perc>input').val("") // val(funcNumValid(t_disc_perc).toFixed(3));
        tr.find('.total_grid_disc_amount>input').val(funcNumValid(t_disc_amount).toFixed(3));
        tr.find('.total_grid_after_disc_amount>input').val(funcNumValid(t_after_disc_amount).toFixed(3));
        tr.find('.total_grid_gst_perc>input').val("") // val(funcNumValid(t_gst_perc).toFixed(3));
        tr.find('.total_grid_gst_amount>input').val(funcNumValid(t_gst_amount).toFixed(3));
        tr.find('.total_grid_fed_perc>input').val("") // val(funcNumValid(t_fed_perc).toFixed(3));
        tr.find('.total_grid_fed_amount>input').val(funcNumValid(t_fed_amount).toFixed(3));
        tr.find('.total_grid_spec_disc_perc>input').val("") // val(funcNumValid(t_spec_disc_perc).toFixed(3));
        tr.find('.total_grid_spec_disc_amount>input').val(funcNumValid(t_spec_disc_amount).toFixed(3));
        tr.find('.total_grid_gross_amount>input').val(funcNumValid(t_gross_amount).toFixed(3));
        // total_grid_amount is net amount
        tr.find('.total_grid_amount>input').val(funcNumValid(t_amount).toFixed(3));
        tr.find('.total_grid_net_tp>input').val("") // val(funcNumValid(t_net_tp).toFixed(3));
        tr.find('.total_grid_last_tp>input').val("") // val(funcNumValid(t_last_tp).toFixed(3));
        tr.find('.total_grid_vend_last_tp>input').val("") // val(funcNumValid(t_vend_last_tp).toFixed(3));
        tr.find('.total_grid_tp_diff>input').val("") // val(funcNumValid(t_tp_diff).toFixed(3));
        tr.find('.total_grid_gp_perc>input').val("") // val(funcNumValid(t_gp_perc).toFixed(3));
        tr.find('.total_grid_gp_amount>input').val(funcNumValid(t_gp_amount).toFixed(3));

        tr.find('.total_grid_perc>input').val(funcNumValid(t_per).toFixed(3));
        tr.find('.total_grid_cost>input').val(funcNumValid(t_cost).toFixed(3));
        tr.find('.total_grid_fc_rate>input').val(funcNumValid(t_fc_rate).toFixed(3));
        tr.find('.total_grid_rate>input').val("") // val(funcNumValid(t_rate).toFixed(3));
        tr.find('.total_grid_vat_amount>input').val(funcNumValid(t_vat_amount).toFixed(3));

        //Summary total
        $('.summary_total_item').val(funcNumValid(total_item).toFixed(3));
        $('.summary_qty_wt').val(funcNumValid(t_qty).toFixed(3));
        $('.summary_amount').val(funcNumValid(t_cost_amount).toFixed(3));
        $('.summary_disc_amount').val(funcNumValid(t_disc_amount).toFixed(3));
        $('.summary_after_disc_amount').val(funcNumValid(t_after_disc_amount).toFixed(3));
        $('.summary_gst_amount').val(funcNumValid(t_gst_amount).toFixed(3));
        $('.summary_fed_amount').val(funcNumValid(t_fed_amount).toFixed(3));
        $('.summary_spec_disc_amount').val(funcNumValid(t_spec_disc_amount).toFixed(3));
        $('.summary_net_amount').val(funcNumValid(t_amount).toFixed(3));

        // TODO : Temporary Fix Only --Adnan (Assumble & Diassamble Form)
        // if($('.t_total').length > 0){
        //     $('.t_total').html(funcNumValid(t_gross_amount).toFixed(3));
        // }

        if (typeof getValuesForDisc !== 'undefined'){ // func make on GRN form
            getValuesForDisc(arr = {});
        }
        if (typeof funcGetOverallNetAmount !== 'undefined'){ // func make on GRN form
            funcGetOverallNetAmount();;
        }
    }
</script>
