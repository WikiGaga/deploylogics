<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Purchase Order Draft</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    </button>
</div>
<div class="modal-body">
    <style>
        #po_list_draft{
            overflow: auto;
            max-height: 75vh;
        }
        #po_list_draft table tbody tr td{
            cursor:pointer;
        }
        #po_list_draft table thead tr{
            background:#b2b2b2;
        }
        #po_list_draft table tbody tr:hover{
            background-color:#f0f8ff;
        }
    </style>
    <div id="po_list_draft">
        <table class="table table-striped- table-bordered table-hover table-checkable" style="width: 100% !important;">
            <thead>
            <tr>
                <th scope="col">
                    <div class="">Sr#</div>
                </th>
                <th scope="col">
                    <div class="">Date</div>
                </th>
                <th scope="col">
                    <div class="">Vendor</div>
                </th>
                <th scope="col">
                    <div class="">Total Amount</div>
                </th>
                <th scope="col">
                    <div class="">Remarks</div>
                </th>
                <th scope="col">
                    <div class="">Action</div>
                </th>
            </tr>
            </thead>
            <tbody class="">
                @php $i = 1;@endphp
                @if(count($data['list']) != 0 )
                @foreach($data['list'] as $row)
                    <tr>
                        <td>
                            <span class="sr_no">{{$i++}}</span>
                            <input type="hidden" class="purchase_order_id" value="{{$row->purchase_order_id}}">
                        </td>
                        <td>
                            <div>{{date('d-m-Y', strtotime(trim(str_replace('/','-',$row->created_at))))}}</div>
                            <div>{{date('h:i:s A', strtotime(trim(str_replace('/','-',$row->created_at))))}}</div>
                        </td>
                        <td>
                            {{isset($row->supplier->supplier_name)?$row->supplier->supplier_name:""}}
                        </td>
                        <td>
                            {{number_format($row->purchase_order_total_amount,3)}}
                        </td>
                        <td>
                            {{$row->purchase_order_remarks}}
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm delRowDraftPOData" style="padding: 5px 5px 5px 8px;">
                                <i class="la la-times"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                @else
                    <tr>
                        <td colspan="5">No Data found in Draft </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
