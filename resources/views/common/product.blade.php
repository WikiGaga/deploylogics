<div class="modal-header">
    <h5 class="modal-title">
        Products
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row kt-mb-10" >
        <div class="col-md-4">
            <div class="kt-input-icon kt-input-icon--left">
                <input type="text" class="form-control form-control-sm" placeholder="Search..." id="generalSearch">
                <span class="kt-input-icon__icon kt-input-icon__icon--left">
                    <span><i class="la la-search"></i></span>
                </span>
            </div>
        </div>
        <div class="col-md-6 text-right"></div>
        <div class="col-md-2 text-right">
            <div class="dropdown dropdown-inline">
                <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="flaticon-more"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" aria-labelledby="dropdownMenu1">
                    <li>
                        <label>
                            <input value="Code" type="checkbox" checked> Code
                        </label>
                    </li>
                    <li>
                        <label>
                            <input value="Name" type="checkbox" checked> Name
                        </label>
                    </li>
                    <li>
                        <label>
                            <input value="Address" type="checkbox" checked> Address
                        </label>
                    </li>
                    <li>
                        <label>
                            <input value="Mobile Number" type="checkbox" checked> Mobile Number
                        </label>
                    </li>
                    <li >
                        <label>
                            <input value="Contact Person" type="checkbox" checked> Contact Person
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <table class="kt-datatable data_table" id="item_datatable" width="100%">
        <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Arabic Name</th>
            <th>UOM</th>
            <th>Packing</th>
            <th>Barcode</th>
            <th>uom id</th>
            <th>packing id</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $item)
        <tr>
            <td>{{$item->product_id}}</td>
            <td>{{$item->product_name}}</td>
            <td>{{$item->product_arabic_name}}</td>
            <td>{{$item->uom_name}}</td>
            <td>{{$item->packing_name}}</td>
            <td>{{$item->product_barcode_barcode}}</td>
            <td>{{$item->uom_id}}</td>
            <td>{{$item->packing_id}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script src="/assets/js/pages/crud/metronic-datatable/base/html-table.js" type="text/javascript"></script>
<script>
   /* $('#item_datatable').on('click', 'tbody>tr', function (e) {
        $('tr#dataEntryForm>td #pd_product_id').val($(this).find('td:eq(0)').text());
        $('tr#dataEntryForm>td:eq(2) input').val($(this).find('td:eq(1)').text());
        $('tr#dataEntryForm>td:eq(3)>input').val($(this).find('td:eq(3)').text());
        $('tr#dataEntryForm>td:eq(4)>input').val($(this).find('td:eq(4)').text());
        $('tr#dataEntryForm>td:eq(1)>input').val($(this).find('td:eq(5)').text());
        $('#kt_modal_KTDatatable_local').find('.modal-content').html('');
        $('#kt_modal_KTDatatable_local').modal('hide');
    });*/
</script>

