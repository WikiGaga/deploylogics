@include('help_lg.po.modal_header')
<div class="modal-body">
    <div id="product_filters">
        <table class="table_lgModal table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline" style="width: 100% !important;">
            <thead class="erp_form__grid_header">
                <tr>
                    <th scope="col">
                        <div class="erp_form__grid_th_title">PO Code</div>
                    </th>
                    <th scope="col">
                        <div class="erp_form__grid_th_title">PO Date</div>
                    </th>
                    <th scope="col">
                        <div class="erp_form__grid_th_title">Supplier</div>
                    </th>
                    <th scope="col">
                        <div class="erp_form__grid_th_title">Total Amount</div>
                    </th>
                    <th scope="col">
                        <div class="erp_form__grid_th_title">Status</div>
                    </th>
                </tr>
            </thead>
            <tbody class="erp_form__grid_body">
                @foreach($data['list'] as $row)
                    @php $i = $loop->iteration; @endphp
                    <tr data-po_id="{{$row->purchase_order_id}}" data-supplier_id="{{$row->supplier_id}}">
                        <td class="code">{{$row->purchase_order_code}}</td>
                        <td>{{date('d-m-Y',strtotime($row->created_at))}}</td>
                        <td class="supplier_name">{{$row->supplier_name}}</td>
                        <td class="total_amount text-right">{{number_format($row->purchase_order_total_amount,3)}}</td>
                        <td>{{$row->po_grn_status}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $('.kt-select2').select2();
    funcGridThResize([]);
    $("#modal_filter_global_search").focus();
</script>
