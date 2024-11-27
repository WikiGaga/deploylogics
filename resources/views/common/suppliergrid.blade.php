<div class="modal-header">
    <h5 class="modal-title">
        Supplier
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <!--begin: Search Form -->
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
    <!--end: Search Form -->
    <table class="kt-datatable data_table" id="suppliergrid_datatable" width="100%">
        <thead>
        <tr>
            <th>Id</th>
            <th>Code</th>
            <th>Name</th>
            <th>Address</th>
            <th>Mobile Number</th>
            <th>Contact Person</th>
        </tr>
        </thead>
        <tbody>
            @foreach($data as $supplier)
                <tr>
                    <td>{{$supplier->supplier_id}}</td>
                    <td>{{$supplier->supplier_code}}</td>
                    <td>{{$supplier->supplier_name}}</td>
                    <td>{{$supplier->supplier_address}}</td>
                    <td>{{$supplier->supplier_phone_1}}</td>
                    <td>{{$supplier->supplier_cheque_beneficry_name}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="/assets/js/pages/crud/metronic-datatable/base/html-table.js" type="text/javascript"></script>


