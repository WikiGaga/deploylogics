
<script>
    var form_modal_type = 'dynamic';
</script>
@include('purchase.product_smart.product_modal_help.script')

<script>
    function funSetProductCustomFilter(arr) {
        var len = arr['len'];
        var product = arr['product'];
        for (var i =0;i<len;i++){
            var row = product[i];
            var sale_rate = !valueEmpty(row['sale_rate']) ? parseFloat(row['sale_rate']).toFixed(3) : '';
            var cost_rate = !valueEmpty(row['cost_rate']) ? parseFloat(row['cost_rate']).toFixed(3) : '';
            var newTr = "<tr data-product_id='"+row['product_id']+"' data-barcode_id='"+row['product_barcode_id']+"'>";
            newTr += "<td>" +
                "<input type='hidden' data-id='product_id' value='"+row['product_barcode_id']+"'>"+
                "<input type='hidden' data-id='product_barcode_id' value='"+row['product_barcode_id']+"'>"+
                "</td>";
            newTr += "<td class='group_item_name'>"+(!valueEmpty(row['group_item_name'])?row['group_item_name']:"")+"</td>";
            newTr += "<td class='barcode'>"+(!valueEmpty(row['product_barcode_barcode'])?row['product_barcode_barcode']:"")+"</td>";
            newTr += "<td class='product_name'>"+(!valueEmpty(row['product_name'])?row['product_name']:"")+"</td>";
            newTr += "<td class='text-right mrp'>"+(!valueEmpty(row['mrp'])?row['mrp']:"")+"</td>";
            newTr += "<td class='text-right sale_rate'>"+sale_rate+"</td>";
            newTr += "<td class='text-right cost_rate'>"+cost_rate+"</td>";
            newTr += "<td class='text-right trade_rate'>"+cost_rate+"</td>";
            newTr += "<td class='supplier_name'>"+(!valueEmpty(row['supplier_name'])?row['supplier_name']:"")+"</td>";
            newTr += "<td class='text-right supplier_rate'>"+(!valueEmpty(row['min_qty'])?row['min_qty']:"")+"</td>";
            newTr += "<td class='text-right supplier_tp'>"+(!valueEmpty(row['depth_qty'])?row['depth_qty']:"")+"</td>";
            newTr += '<td class="text-center">\n' +
                '                            <div style="position: relative;top: -5px;">\n' +
                '                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">\n' +
                '                                    <input type="checkbox" class="addCheckedProduct" data-id="add_prod" >\n' +
                '                                    <span></span>\n' +
                '                                </label>\n' +
                '                            </div></td>';
            newTr += "</tr>";

            $('table.table_pitModal').find('tbody.erp_form__grid_body').append(newTr);
        }
    }

    function funcAddSelectedProductToFormGrid(tr){
        var cloneTr = tr.clone();
        var table_pit_list = $('table.table_pit_list');
        var tr = table_pit_list.find('.erp_form__grid_header>tr:first-child');
        var barcode = $(cloneTr).find('.barcode').text();
        tr.find('#pd_barcode').val(barcode);

        var form_type = $('#form_type').val();
        var supplier_id = $('#supplier_id').val();
        var formData = {
            form_type : form_type,
            val : barcode,
        }
        if (!valueEmpty(supplier_id)) {
            formData.supplier_id = supplier_id;
        }
        initBarcode(13, tr, form_type, formData)

        closeModal();
    }
</script>
