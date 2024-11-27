<div class="modal-header" style="padding: 8px 18px;">
    <div class="row" style="width:100%">
        <div class="col-lg-4">
            <h5 class="modal-title">
                {{ $data['title'] }} Help
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
                @foreach($data['headings'] as $heading)
                    <li >
                        <label>
                            <input value="{{$heading}}" type="checkbox" checked> {{$heading}}
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>
        <table class="kt-datatable data_table help_datatable" data-help="{{$data['caseType']}}" width="100%">
            <thead>
            <tr>
                @foreach($data['hiddenFields'] as $hiddenField)
                    <th>{{$hiddenField}}</th>
                @endforeach
                @foreach($data['headings'] as $heading)
                    <th>{{$heading}}</th>
                @endforeach
            </tr>
            </thead>
        </table>
    </div>
    <div class="kt-scroll" id="KTScrollContainer" data-scroll="true" style="max-height: 350px">
        <table class="kt-datatable data_table help_datatable" id="help_datatable_{{$data['caseType']}}" data-help="{{$data['caseType']}}" width="100%">
            <tbody>
            @foreach($data['table'] as $key=>$tr)
                <tr>
                    @foreach($data['hiddenFields'] as $td)
                        <td>{{$tr->$td}}</td>
                    @endforeach
                    @foreach($data['columnName'] as $td)
                        <td>{{$tr->$td}}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    @for($i=1;$i <= count($data['hiddenFields']);$i++)
        .help_datatable>thead>tr>th:nth-child({{$i}}),
        .help_datatable>tbody>tr>td:nth-child({{$i}}) {
            display: none;
        }
    @endfor
</style>
<script src="/assets/js/pages/crud/metronic-datatable/base/html-table.js" type="text/javascript"></script>
<script>
    var caseType = "{{$data['caseType']}}";
    switch (caseType) {
        case 'supplierHelp' : {
            selectSupplier();
        }
        case 'RejectReasonHelp' : {
            RejectReason();
        }
        case 'productHelp' : {
            selectProduct();
        }
        case 'demandApprovalHelp' : {
            selectDemandApproval();
        }
        case 'lpoPoHelp' : {
            selectLpo();
        }
        case 'poHelp' : {
            selectPO();
        }
        case 'comparativeQuotationHelp' : {
            selectComparativeQuotation();
        }
        case 'quotationHelp' : {
            selectQuotation();
        }
        case 'lpoPoQuotationHelp' : {
            selectLpo();
        }
        case 'customerHelp' : {
            selectCustomer();
        }
    }
    function closeModal(){
        $('#kt_modal_KTDatatable_local').find('.modal-content').empty();
        $('#kt_modal_KTDatatable_local').find('.modal-content').html(' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
        $('#kt_modal_KTDatatable_local').modal('hide');
    }
    new PerfectScrollbar('#KTScrollContainer');
</script>

