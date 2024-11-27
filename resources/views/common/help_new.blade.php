<div class="modal-header" style="padding: 8px 18px;">
    <div class="row" style="width:100%">
        <div class="col-lg-4">
            <h5 class="modal-title">
                {{ $data['title'] }} Help
            </h5>
        </div>
        <div class="col-lg-6">
            <div class="kt-input-icon kt-input-icon--left">
                <input type="text" autocomplete="off" class="form-control form-control-sm" placeholder="Search..." id="generalSearch" >
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
    <script>
        var dataFields = {
            @foreach($data['table_colums'] as $key=>$heading)
            "{{$key}}": "{{$heading}}",
            @endforeach
        };
        var dataHideFields = {
            @foreach($data['hiddenFields'] as $key=>$heading)
            "{{$key}}": "{{$heading}}",
            @endforeach
        };
    </script>
    <!--begin: Search Form -->
    <!--end: Search Form -->
    <div class="data_table_header">
        <div class="dropdown dropdown-inline" style="position: absolute;z-index: 9999;right: 5px;top: -10px;">
            <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                <i class="flaticon-more" style="color: #666666;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" aria-labelledby="dropdownMenu1">
                @foreach($data['table_colums'] as $key=>$heading)
                    <li >
                        <label>
                            <input value="{{$key}}" type="checkbox" checked> {{$heading}}
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <!--begin: Datatable -->
    <div class="kt-scroll" id="KTScrollContainer" data-scroll="true" {{--style="position: absolute;"--}}>
        <div id="help_datatable_{{$data['caseType']}}" class="kt-datatable ajax_data_table help_datatable modal_help" data-url="{{ action('Common\DataTableController@modalHelpOpen',$data['case']) }}"  data-help="{{$data['caseType']}}" width="100%"></div>
    </div>
    <!--end: Datatable -->
</div>

<style>
    @for($i=1;$i <= count($data['hiddenFields']);$i++)
        .help_datatable>thead>tr>th:nth-child({{$i}}),
        .help_datatable>tbody>tr>td:nth-child({{$i}}) {
            display: none;
        }
    @endfor
    tr.kt-datatable__row.highlight>td {
        background: #f0f8ff !important;
    }
    .kt-datatable{
        height: 350px !important;
    }
    .kt-datatable__table{
        max-height: 100% !important;
        overflow-y: scroll !important;
        width: 100% !important;
        visibility: hidden;
        position: sticky;
    }
    .kt-datatable__table>thead{
        position: sticky;
        top: 0px;
        z-index: 9999;
    }
    .modal-content{
        height: 425px !important;
    }
    /*
        scrollbar styling
    */
    .kt-datatable__table::-webkit-scrollbar-track
    {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        background-color: #ffffff;
        border-radius: 10px;

    }

    .kt-datatable__table::-webkit-scrollbar
    {
        width: 8px;
        background-color: #ffffff;
    }

    .kt-datatable__table::-webkit-scrollbar-thumb
    {
        background-color: #7f7f7f;
    }
    .kt-datatable__table:hover,
    .kt-datatable__table:focus {
        visibility: visible;
    }
</style>
<script src="{{ asset('js/pages/js/data-help-ajax.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        document.getElementById("generalSearch").focus();
    });
</script>
<script>
    new PerfectScrollbar('#KTScrollContainer');

</script>

