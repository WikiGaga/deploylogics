<style>
    .purchasing_products_details .product_detail{
        border: 2px solid #c8c8c8;
        margin-bottom: 0 !important;
    }
    .purchasing_products_details .product_detail th {
        background: #f4f7fe;
        padding:5px;
        font-size: 12px;
        font-family: 'Roboto';
        border-right: 1px solid #d8d1e3;
        vertical-align: top;

    }
    .purchasing_products_details .product_detail tr:nth-child(odd) td {
        background: #fefefe;
        padding:5px;
        font-size: 12px;
        font-family: 'Roboto';
        border-right: 1px solid #d8d1e3;
        vertical-align: top;
        font-weight: 400;
    }
    .purchasing_products_details .product_detail tr:nth-child(even) td {
        background: #f7f7f7;
        padding:5px;
        font-size: 12px;
        font-family: 'Roboto';
        border-right: 1px solid #d8d1e3;
        vertical-align: top;
        font-weight: 400;
    }
</style>
{{--{{dd($data['items'])->toArray()}}--}}
<table class="table table-bordered product_detail">
    <thead>
        <tr>
            <th width="10%">Barcode</th>
            <th width="20%">Name</th>
            <th width="5%">UOM</th>
            <th width="5%">Packing</th>
            <th class="text-center" width="5%">Purc Qty</th>
            @foreach($data['branch'] as $branch)
                <th class="text-center" width="10%">{{$branch->branch_short_name}}<br><span style="font-size: 10px;color: #f44336;">{{$branch->stock_no}}</span></th>
            @endforeach
            <th class="text-center" width="5%">Diff Qty</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data['items'] as $kk=>$item)
        @php $k = $kk+1 @endphp
        <tr>
            <td>
                {{$item->purchasing_dtl_dtl_barcode}}
                <input type="hidden" name="pd[{{$k}}][purchasing_dtl_dtl_id]" value="{{$item->purchasing_dtl_dtl_id}}">
                <input type="hidden" name="pd[{{$k}}][barcode_id]" value="{{$item->product_barcode_id}}">
                <input type="hidden" name="pd[{{$k}}][product_id]" value="{{$item->product_id}}">
                <input type="hidden" name="pd[{{$k}}][uom_id]" value="{{$item->uom_id}}">
                <input type="hidden" name="pd[{{$k}}][barcode]" value="{{$item->purchasing_dtl_dtl_barcode}}">
                <input type="hidden" name="pd[{{$k}}][packing]" value="{{$item->purchasing_dtl_dtl_packing}}">
                <input type="hidden" name="pd[{{$k}}][qty]" value="{{$item->purchasing_dtl_dtl_quantity}}">
                <input type="hidden" name="pd[{{$k}}][rate]" value="{{$item->purchasing_dtl_dtl_rate}}">
                <input type="hidden" name="pd[{{$k}}][amount]" value="{{$item->purchasing_dtl_dtl_amount}}">
                <input type="hidden" name="pd[{{$k}}][disc_perc]" value="{{$item->purchasing_dtl_dtl_disc_percent}}">
                <input type="hidden" name="pd[{{$k}}][disc_amount]" value="{{$item->purchasing_dtl_dtl_disc_amount}}">
                <input type="hidden" name="pd[{{$k}}][vat_perc]" value="{{$item->purchasing_dtl_dtl_vat_percent}}">
                <input type="hidden" name="pd[{{$k}}][vat_amount]" value="{{$item->purchasing_dtl_dtl_vat_amount}}">
                <input type="hidden" name="pd[{{$k}}][net_amount]" value="{{$item->purchasing_dtl_dtl_net_amount}}">
            </td>
            <td>{{$item->product->product_name}}</td>
            <td>{{$item->uom->uom_name}}</td>
            <td>{{$item->purchasing_dtl_dtl_packing}}</td>
            <td class="text-center totalQty">{{$item->purchasing_dtl_dtl_quantity}}</td>
            @foreach($data['branch'] as $bk=>$branch)
                <td>
                    <input type="hidden" name="pd[{{$k}}][branch][{{$bk}}][stock_no]" value="{{$branch->stock_no}}">
                    <input type="hidden" name="pd[{{$k}}][branch][{{$bk}}][id]" value="{{$branch->branch_id}}">
                    <input type="text" name="pd[{{$k}}][branch][{{$bk}}][qty]" class="branch_qty form-control erp-form-control-sm validNumber validOnlyFloatNumber OnlyEnterAllow">
                </td>
            @endforeach
            <td class="text-center diffQty">{{$item->purchasing_dtl_dtl_quantity}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    $('input').attr('autocomplete', 'off');
    $('.OnlyEnterAllow').keypress(validateNumber);
</script>
