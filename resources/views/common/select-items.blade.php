<div class="modal-header" style="padding: 8px 18px;">
    <div class="row" style="width:100%">
        <div class="col-lg-4">
            <h5 class="modal-title">
                Select Items Help
            </h5>
        </div>
        <div class="col-lg-6">
            <div class="kt-input-icon kt-input-icon--left">
                <input type="text" class="form-control form-control-sm" placeholder="Search..." id="generalSearch">
                <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
            </div>
        </div>
        <div class="col-lg-2">

        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="top: 10px;right: 10px;position: absolute;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
<div class="modal-body">
    <!--begin: Search Form -->
    <!--end: Search Form -->
    <div class="data_table_header">
        <div class="dropdown dropdown-inline" style="position: absolute;z-index: 9999;right: 5px;top: 8px;">
            <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                <i class="flaticon-more" style="color: #666666;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" aria-labelledby="dropdownMenu1">

            </ul>
        </div>
        <table class="kt-datatable data_table help_datatable" data-help="" width="100%">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Type</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="kt-scroll" id="KTScrollContainer" data-scroll="true" style="max-height: 350px">
        <table class="kt-datatable data_table help_datatable" id="help_datatable" data-help="" width="100%">
            <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{$item['code']}}</td>
                    <td>{{$item['name']}}</td>
                    <td>{{$item['type']}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="/assets/js/pages/crud/metronic-datatable/base/html-table.js" type="text/javascript"></script>
<script>
$('#KTScrollContainer').on('click', 'tbody>tr', function (e) {
    var tr = '';
    var td = '';
    td += '<td style="padding: 5px 0 5px 70px !important;">'+$(this).find('td[data-field="Code"]').text()+'</td>';
    td += '<td style="padding: 5px 0 5px 70px !important;">'+$(this).find('td[data-field="Name"]').text()+'</td>';
    td += '<td style="padding: 5px 0 5px 70px !important;">'+$(this).find('td[data-field="Type"]').text()+'</td>';
    tr = '<tr>'+td+'</tr>';
    $('#repeated_datasm').append(tr);
});
new PerfectScrollbar('#KTScrollContainer');
</script>

