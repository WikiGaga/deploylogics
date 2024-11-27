{{--Purchase order form.blade--}}
<script>
    var loasing = false;
    if(loasing){
        var url = $('.erp_form__grid_body').attr('data-url');
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'GET',
            url: url,
            data:{_token: CSRF_TOKEN},
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            success: function(response, status){
                console.log(response.data['rows']);
                if(response.status == 'success') {
                    var total_length = $('#repeated_data>tr').length;
                    for(var p=0; p < response.data['rows'].length; p++ ){
                        total_length++;
                        var  row = response.data['rows'][p];

                        var tr = '<tr>\n' +
                            '   <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>\n' +
                            '      <input type="text" value="'+total_length+'" name="pd['+total_length+'][sr_no]"  class="form-control erp-form-control-sm handle" readonly>\n' +
                            '      <input type="hidden" name="pd['+total_length+'][purchase_order_dtl_id]" data-id="purchase_order_dtl_id" value="'+row['purchase_order_dtl_id']+'" class="purchase_order_dtl_id form-control erp-form-control-sm handle" readonly>\n' +
                            '      <input type="hidden" name="pd['+total_length+'][product_id]" data-id="product_id" value="'+row['product_id']+'" class="product_id form-control erp-form-control-sm handle" readonly>\n' +
                            '      <input type="hidden" name="pd['+total_length+'][uom_id]" data-id="uom_id" value="'+row['uom_id']+'" class="uom_id form-control erp-form-control-sm handle" readonly>\n' +
                            '      <input type="hidden" name="pd['+total_length+'][product_barcode_id]" data-id="product_barcode_id" value="'+row['product_barcode_id']+'" class="product_barcode_id form-control erp-form-control-sm handle" readonly>\n' +
                            '      <input type="hidden" name="pd['+total_length+'][lpo_id]" data-id="lpo_id" value="" class="lpo_id form-control erp-form-control-sm" readonly>\n' +
                            '      <input type="hidden" name="pd['+total_length+'][comparative_quotation_id]" data-id="comparative_quotation_id" value="" class="comparative_quotation_id form-control erp-form-control-sm" readonly>\n' +
                            '   </td>\n' +
                            '   <td><input type="text" data-id="pd_barcode" name="pd['+total_length+'][pd_barcode]" value="'+row['product_barcode_barcode']+'" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>\n' +
                            '   <td><input type="text" data-id="product_name" name="pd['+total_length+'][product_name]" value="'+row['product_name']+'" class="product_name form-control erp-form-control-sm" readonly></td>\n' +
                            '   <td>\n' +
                            '      <select class="pd_uom field_readonly form-control erp-form-control-sm" data-id="pd_uom" name="pd['+total_length+'][pd_uom]">\n' +
                            '      <option value="'+row['uom_id']+'">'+row['uom_name']+'</option>\n' +
                            '      </select>\n' +
                            '   </td>\n' +
                            '   <td><input type="text" data-id="pd_packing" name="pd['+total_length+'][pd_packing]" value="'+row['purchase_order_dtlpacking']+'" class="pd_packing form-control erp-form-control-sm" readonly></td>\n' +
                            '   <td><input type="text" data-id="remarks" name="pd['+total_length+'][remarks]" value="" class="form-control erp-form-control-sm tb_moveIndex"></td>\n' +
                            '   <td><input type="text" name="pd['+total_length+'][quantity]" data-id="quantity"  value="" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>\n' +
                            '   <td><input type="text" name="pd['+total_length+'][foc_qty]" data-id="foc_qty"  value="" class="tb_moveIndex foc_qty form-control erp-form-control-sm validNumber"></td>\n' +
                            '   <td><input type="text" name="pd['+total_length+'][fc_rate]" data-id="fc_rate"  value="" class="fc_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>\n' +
                            '   <td><input type="text" name="pd['+total_length+'][rate]" data-id="rate"  value="" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>\n' +
                            '   <td><input type="text" name="pd['+total_length+'][amount]" data-id="amount"  value="" class="tblGridCal_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>\n' +
                            '   <td><input type="text" name="pd['+total_length+'][dis_perc]" data-id="dis_perc"  value="" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>\n' +
                            '   <td><input type="text" name="pd['+total_length+'][dis_amount]" data-id="dis_amount"  value="" class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>\n' +
                            '   <td><input type="text" name="pd['+total_length+'][vat_perc]" data-id="vat_perc"  value="" class="tblGridCal_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>\n' +
                            '   <td><input type="text" name="pd['+total_length+'][vat_amount]" data-id="vat_amount"  value="" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>\n' +
                            '   <td><input type="text" name="pd['+total_length+'][gross_amount]" data-id="gross_amount"  value="" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>\n' +
                            '   <td class="text-center">\n' +
                            '      <div class="btn-group btn-group btn-group-sm" role="group">\n' +
                            '         <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>\n' +
                            '      </div>\n' +
                            '   </td>\n' +
                            '</tr>';
                        $('.erp_form__grid_body').append(tr);
                    }
                    addDataInit();

                    $(".date_inputmask").inputmask("99-99-9999", {
                        "mask": "99-99-9999",
                        "placeholder": "dd-mm-yyyy",
                        autoUnmask: true
                    });
                }
                else{
                    toastr.error(response.message);
                }
            },
            error: function(response,status) {
                console.log(response);
            },
        });
    }

</script>
{{-- all grid calculation--}}
<script>
    $('.erp_form__grid_body>tr').each(function(){
        let thix = $(this);
        let gross_amount = 0;

        /*
            qty
        */
        var qty = thix.find('.tblGridCal_qty').val();
        qty = qty.replace(",","");
        qty = Number(qty);
        if(!qty){ qty = 1; thix.find('.tblGridCal_qty').val(qty);}

        /*
            rate
        */
        var rate = thix.find('.tblGridCal_rate').val();
        rate = rate.replace(",","");
        rate = Number(rate);
        if(!rate){ rate = 0; thix.find('.tblGridCal_rate').val(parseFloat(rate).toFixed(3));}

        /*
            amount calculate
        */
        var amount = qty * rate;
        thix.find('.tblGridCal_amount').val(parseFloat(amount).toFixed(3));

        gross_amount = 0 + amount;


        /*
            discount calculate
        */
        var discount_perc = thix.find('.tblGridCal_discount_perc').val();
        discount_perc = discount_perc.replace(",","");
        discount_perc = Number(discount_perc);
        if(!discount_perc){ discount_perc = 0; }
        if(discount_perc != 0){
            var discount_amount = amount / 100 * discount_perc;

            thix.find('.tblGridCal_discount_amount').val(parseFloat(discount_amount).toFixed(3));
        }else{
            var discount_amount = thix.find('.tblGridCal_discount_amount').val();
            discount_amount = discount_amount.replace(",","");
            discount_amount = Number(discount_amount);
            if(!discount_amount){ discount_amount = 0;}

            if(discount_amount != 0){
                var discount_perc = discount_amount / 100 * amount;

                thix.find('.tblGridCal_discount_perc').val(parseFloat(discount_perc).toFixed(2));

            }else{
                thix.find('.tblGridCal_discount_perc').val(parseFloat(0).toFixed(2));
                thix.find('.tblGridCal_discount_amount').val(parseFloat(0).toFixed(3));
            }
        }

        gross_amount = gross_amount - discount_amount;

        /*
            vat calculate
        */
        var vat_perc = thix.find('.tblGridCal_vat_perc').val();
        vat_perc = vat_perc.replace(",","");
        vat_perc = Number(vat_perc);
        if(!vat_perc){ vat_perc = 0; }
        if(vat_perc != 0){
            var vat_amount = amount / 100 * vat_perc;

            thix.find('.tblGridCal_vat_amount').val(parseFloat(vat_amount).toFixed(3));
        }else{
            var vat_amount = thix.find('.tblGridCal_vat_amount').val();
            vat_amount = vat_amount.replace(",","");
            vat_amount = Number(vat_amount);
            if(!vat_amount){ vat_amount = 0;}

            if(vat_amount != 0){
                var vat_perc = vat_amount / 100 * amount;

                thix.find('.tblGridCal_vat_perc').val(parseFloat(vat_perc).toFixed(2));

            }else{
                thix.find('.tblGridCal_vat_perc').val(parseFloat(0).toFixed(2));
                thix.find('.tblGridCal_vat_amount').val(parseFloat(0).toFixed(3));
            }
        }

        gross_amount = gross_amount + vat_amount;

        thix.find('.tblGridCal_gross_amount').val(parseFloat(gross_amount).toFixed(3));
    });
</script>
