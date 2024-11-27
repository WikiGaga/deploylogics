@if(isset($data['message']))
    <div style="    background: #ecf0f3;
    padding: 0px;
    color: #000;
    font-weight: 500;">No Data Available</div>
@endif
@if(!isset($data['message']))
    <style>
        .product_block .single_product_detail{
            border: 2px solid #c8c8c8;
            margin-bottom: 0 !important;
        }
        /*.product_block .single_product_detail th,
        .product_block .single_product_detail td {
            background: #efefef;
            padding: 1px 5px;
            font-size: 12px;
            font-family: 'Roboto';
            border-right: 2px solid #e1e1e1;
            vertical-align: top;
        }*/
        .product_block .single_product_detail th {
            background: #f4f7fe;
            padding: 1px 5px;
            font-size: 12px;
            font-family: 'Roboto';
            border-right: 1px solid #d8d1e3;
            vertical-align: top;

        }
        .product_block .single_product_detail td {
            background: #f4f7fe;
            padding: 1px 5px;
            font-size: 12px;
            font-family: 'Roboto';
            border-right: 1px solid #d8d1e3;
            vertical-align: top;
            font-weight: 400;
        }
        .branch_name{
            background: #efefef;
        }
        .erp_form__grid {
            border: 2px solid #c8c8c8;
            border-top: 0 solid;
            border-bottom: 0 solid;
        }
        .btn-brand-c{
            background-color: #607d8b;
            border-color: #607d8b;
            color: #fff;
        }
        .btn-brand-c:hover{
            background-color: #94a5ad;
            border-color: #94a5ad;
            color: #fff;
        }
        .btn-brand-c:focus{
            box-shadow: unset !important;
        }
    </style>
@endif
@php $alpha = 'a'; @endphp
@foreach($data as $items)
    @php $totalQty = 0; @endphp
    <div class="product_block">
        @foreach($items['branches'] as $i=>$item_bra)
            <input type="hidden" name="dtl[{{$alpha}}][branch][{{$i}}][stock_no]" value="{{$item_bra['stock_no']}}">
            <input type="hidden" name="dtl[{{$alpha}}][branch][{{$i}}][id]" value="{{$item_bra['id']}}">
            <input type="hidden" name="dtl[{{$alpha}}][branch][{{$i}}][qty]" value="{{$item_bra['qty']}}">
            <input type="hidden" name="dtl[{{$alpha}}][branch][{{$i}}][stock_id]" value="{{$item_bra['stock_id']}}">
        @endforeach
        <input type="hidden" name="dtl[{{$alpha}}][input_purc_qty]" class="input_purc_qty" value="0">
        <input type="hidden" name="dtl[{{$alpha}}][product_id]" value="{{$items['products'][0]['product_id']}}">
        <input type="hidden" name="dtl[{{$alpha}}][product_barcode_id]" value="{{$items['products'][0]['product_barcode_id']}}">
        <input type="hidden" name="dtl[{{$alpha}}][uom_id]" value="{{$items['products'][0]['uom_id']}}">
        <input type="hidden" class="copy_barcode" name="dtl[{{$alpha}}][product_barcode_barcode]" value="{{$items['products'][0]['product_barcode_barcode']}}">
        <input type="hidden" name="dtl[{{$alpha}}][packing]" value="{{$items['products'][0]['demand_dtl_packing']}}">
        <table class="table table-bordered single_product_detail">
            <tbody>
            <tr>
                <th rowspan="2" width="7%" style="vertical-align: middle; text-align: center;padding: 0;">
                    <button type="button" class="btn btn-brand-c btn-elevate btn-icon product_table_toggle" style="width: 30px;height: 28px;margin-bottom: 3px; "><i class="la la-arrow-circle-up rotate"></i></button>
                    <button type="button" class="btn btn-brand-c btn-elevate btn-icon product_copy" style="width: 30px;height: 28px;margin-bottom: 3px; "><i class="la la-copy"></i></button>
                </th>
                <th width="10%">Barcode</th>
                <th width="20%">Name</th>
                <th width="5%">UOM</th>
                <th width="5%">Packing</th>
                @foreach($items['branches'] as $name)
                    <th class="text-center branch_name " width="10%">{{$name['name']}}<br><span style="font-size: 10px;color: #f44336;">{{$name['stock_no']}}</span></th>
                @endforeach
                <th class="text-center" width="5%">Total Qty</th>
                <th class="text-center" width="5%">Purc Qty</th>
                <th class="text-center" width="5%">Diff Qty</th>
            </tr>
            <tr>
                <td>{{$items['products'][0]['product_barcode_barcode']}}</td>
                <td>{{$items['products'][0]['product_name']}}</td>
                <td>{{$items['products'][0]['uom_name']}}</td>
                <td>{{$items['products'][0]['demand_dtl_packing']}}</td>
                @foreach($items['branches'] as $qty)
                    @php $totalQty += (float)$qty['qty'];@endphp
                    <td class="text-center branch_name">{{$qty['qty']}}</td>
                @endforeach
                <td class="text-center total_qty">{{$totalQty}}</td>
                <td class="text-center purc_qty">0</td>
                <td class="text-center diff_qty">{{$totalQty}}</td>
            </tr>
            </tbody>
        </table>
        <div class="form-group-block" style="margin-bottom: 10px">
            <div class="erp_form___block" style="display: none">
                <div class="table-scroll form_input__block">
                    <table prefix="dtl[{{$alpha}}][pd]" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                        <thead class="erp_form__grid_header">
                        <tr>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Sr.</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                    <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                    <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                    <input id="uom_id" readonly type="hidden" class="uom_id form-control erp-form-control-sm">
                                    <input id="grn_supplier_id" readonly type="hidden" class="grn_supplier_id form-control erp-form-control-sm handle">
                                    <input id="grn_dtl_po_rate" readonly type="hidden" class="grn_dtl_po_rate form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">
                                    Barcode
                                    <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                        <i class="la la-barcode"></i>
                                    </button>
                                </div>
                                <div class="erp_form__grid_th_input">
                                    <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Product Name</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="product_name" readonly type="text" class="product_name form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">UOM</div>
                                <div class="erp_form__grid_th_input">
                                    <select id="pd_uom" class="pd_uom tb_moveIndex form-control erp-form-control-sm">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </th>
                            <th cope="col">
                                <div class="erp_form__grid_th_title">Packing</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="pd_packing" readonly type="text" class="pd_packing form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Qty</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">FOC Qty</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="foc_qty" type="text" class="validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">FC Rate</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="fc_rate" type="text" class="fc_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Rate</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="rate" type="text" class="tblGridCal_rate tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Amount</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="amount" type="text" class="tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Disc %</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="dis_perc" type="text" class="tblGridCal_discount_perc tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Disc Amt</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="dis_amount" type="text" class="tblGridCal_discount_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">VAT %</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="vat_perc" type="text" class="tblGridCal_vat_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">VAT Amt</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="vat_amount" type="text" class="tblGridCal_vat_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Gross Amt</div>
                                <div class="erp_form__grid_th_input">
                                    <input id="gross_amount" readonly type="text" class="tblGridCal_gross_amount validNumber form-control erp-form-control-sm">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Action</div>
                                <div class="erp_form__grid_th_btn">
                                    <button type="button" class="addData tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                        <i class="la la-plus"></i>
                                    </button>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="erp_form__grid_body">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @php $alpha = ++$alpha; @endphp
@endforeach
{{--<div class="product_block">
    <table class="table table-bordered single_product_detail">
        <thead>
        <tr>
            <th width="100px">Barcode</th>
            <th width="200px">Name</th>
            <th width="50px">UOM</th>
            <th width="50px">Packing</th>
            <th width="100px">Atayebat</th>
            <th width="50px">Total Qty</th>
            <th width="50px">Purc Qty</th>
            <th width="50px">Diff Qty</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>53030157000</td>
            <td>JAL CANNES T SHIRT UO</td>
            <td>PCS</td>
            <td>1</td>
            <td>20</td>
            <td>20</td>
            <td>0</td>
            <td>20</td>
        </tr>
        </tbody>
    </table>
    <div class="form-group-block">
        <div class="erp_form___block">
            <div class="table-scroll form_input__block">
                <table prefix="b" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                    <thead class="erp_form__grid_header">
                    <tr>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">Sr.</div>
                            <div class="erp_form__grid_th_input">
                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                <input id="uom_id" readonly type="hidden" class="uom_id form-control erp-form-control-sm">
                                <input id="grn_supplier_id" readonly type="hidden" class="grn_supplier_id form-control erp-form-control-sm handle">
                                <input id="grn_dtl_po_rate" readonly type="hidden" class="grn_dtl_po_rate form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">
                                Barcode
                                <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                    <i class="la la-barcode"></i>
                                </button>
                            </div>
                            <div class="erp_form__grid_th_input">
                                <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">Product Name</div>
                            <div class="erp_form__grid_th_input">
                                <input id="product_name" readonly type="text" class="product_name form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">UOM</div>
                            <div class="erp_form__grid_th_input">
                                <select id="pd_uom" class="pd_uom tb_moveIndex form-control erp-form-control-sm">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </th>
                        <th cope="col">
                            <div class="erp_form__grid_th_title">Packing</div>
                            <div class="erp_form__grid_th_input">
                                <input id="pd_packing" readonly type="text" class="pd_packing form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">Qty</div>
                            <div class="erp_form__grid_th_input">
                                <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">FOC Qty</div>
                            <div class="erp_form__grid_th_input">
                                <input id="foc_qty" type="text" class="validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">FC Rate</div>
                            <div class="erp_form__grid_th_input">
                                <input id="fc_rate" type="text" class="fc_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">Rate</div>
                            <div class="erp_form__grid_th_input">
                                <input id="rate" type="text" class="tblGridCal_rate tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">Amount</div>
                            <div class="erp_form__grid_th_input">
                                <input id="amount" type="text" class="tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">Disc %</div>
                            <div class="erp_form__grid_th_input">
                                <input id="dis_perc" type="text" class="tblGridCal_discount_perc tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">Disc Amt</div>
                            <div class="erp_form__grid_th_input">
                                <input id="dis_amount" type="text" class="tblGridCal_discount_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">VAT %</div>
                            <div class="erp_form__grid_th_input">
                                <input id="vat_perc" type="text" class="tblGridCal_vat_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">VAT Amt</div>
                            <div class="erp_form__grid_th_input">
                                <input id="vat_amount" type="text" class="tblGridCal_vat_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">Gross Amt</div>
                            <div class="erp_form__grid_th_input">
                                <input id="gross_amount" readonly type="text" class="tblGridCal_gross_amount validNumber form-control erp-form-control-sm">
                            </div>
                        </th>
                        <th scope="col">
                            <div class="erp_form__grid_th_title">Action</div>
                            <div class="erp_form__grid_th_btn">
                                <button type="button" class="addData tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                    <i class="la la-plus"></i>
                                </button>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="erp_form__grid_body">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>--}}
@if(count($data) !== 0)
    <script>
        $(document).find('input').attr('autocomplete', 'off');
        $( ".erp_form__grid" ).colResizable({ disable : true });
        var colWidth = [35,100,130,70,45,50,40,40,50,70,50,50,50,50,70,50];
        $( ".erp_form__grid" ).colResizable({
            headerOnly: true,
            liveDrag:true,
            // gripInnerHtml:"<div class='grip'></div>",
            resizeMode:'overflow',
            draggingClass:"dragging",
            fixedWidths: colWidth
        });
    </script>
@endif

