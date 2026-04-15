@extends('layouts.template')
@section('title', 'Import Data')

@section('pageCSS')
    <style>
        /* Ensures Select2 matches your theme width */
        .select2-container--default .select2-selection--single {
            height: calc(1.5em + 1.3rem + 2px) !important;
            padding: 0.65rem 1rem !important;
            border-color: #ebedf2 !important;
        }
        .select2.select2-container {
            width: 100% !important;
            min-width: 170px !important;
        }
    </style>
@endsection

@section('content')
    @permission($data['permission'])
        <div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
            
            {{-- Section 1: CSV File Import (Original Code) --}}
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <form id="import_data_form" class="erp_form_validation kt-form" method="post" enctype="multipart/form-data" action="{{ route('import.store') }}">
                        @csrf
                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">Import via CSV File</h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="form-group-block row">
                                            <label class="col-lg-5 erp-col-form-label" for="tableName">Select Table <span class="required">*</span></label>
                                            <div class="col-lg-7">  
                                                <div class="erp-select2 form-group">
                                                    <select name="table_name" id="tableName" class="form-control kt-select2 erp-form-control-sm">
                                                        <option value="0">Select</option>
                                                        @foreach($data['table_list'] as $table_list)
                                                            <option value="{{strtolower($table_list->table_name)}}">{{strtolower($table_list->table_name)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2"></div>
                                    <div class="col-lg-5">
                                        <div class="form-group-block row">
                                            <label class="col-lg-5 erp-col-form-label" for="chooseFile">Select File <span class="required">*</span></label>
                                            <div class="col-lg-7">
                                                <input type="file" class="form-control-file" id="chooseFile" name="csv_file">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4" id="tblTopRows"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Section 2: Live to Local Sync (New Transactional Logic) --}}
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title text-danger">
                            <i class="flaticon-refresh text-danger"></i> Live to Local Data Sync (Oracle)
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <form id="dumpForm" action="{{ route('import.dump.data') }}" method="POST" class="kt-form">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="form-group mb-0">
                                    <label>Search and Select Table to Sync from Live Server:</label>
                                    <select name="table_name" id="tableSelector" class="form-control kt-select2" required>
                                        <option value="">-- Search Table Name --</option>
                                        @foreach($data['table_list'] as $table_list)
                                            <option value="{{strtolower($table_list->table_name)}}">{{strtolower($table_list->table_name)}}</option>
                                        @endforeach
                                    </select>
                                    <span class="form-text text-muted">Warning: This will clear local data and pull fresh data from 74.208.221.142.</span>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <button type="submit" id="submitBtn" class="btn btn-brand btn-elevate btn-icon-sm mt-4">
                                    <i class="la la-copy"></i>
                                    <span class="btn-text">Start Data Dump</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    @endpermission
@endsection

@section('customJS')
    {{-- Original JS Logic --}}
    <script src="{{ asset('js/pages/js/development/data-import.js') }}" type="text/javascript"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2 for the Searchable Table Sync
            $('#tableSelector').select2({
                placeholder: "Search for a table name...",
                allowClear: true,
                width: '100%'
            });

            // Disable button on click and show spinner for the Dump Form
            $('#dumpForm').on('submit', function() {
                let btn = $('#submitBtn');
                
                // Final confirmation before clearing data
                if(!confirm("Are you sure? Local data will be cleared. Transactional safety is enabled.")) {
                    return false;
                }

                btn.prop('disabled', true); 
                btn.addClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light');
                btn.find('.btn-text').text('Processing Oracle Transaction...');
                btn.find('.spinner-border').removeClass('d-none'); 
                return true;
            });

            // Handle original File Upload Change
            $('#chooseFile').on('change', function() {
                var thix = $(this);
                var file = document.querySelector("#chooseFile").files[0];
                if (!file) return;

                var name = file.name;
                var ext = name.split('.').pop().toLowerCase();
                var size = file.size || file.fileSize;

                if ($.inArray(ext, ['csv']) == -1) {
                    toastr.error("Invalid File Selection (Allowed CSV)");
                    thix.val('');
                } else if (size > 10000000) {
                    toastr.error("File size exceed (Max 10Mb)");
                    thix.val('');
                } else {
                    var formData = new FormData(document.querySelector("#import_data_form"));
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: "{{ route('import.selected') }}",
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        data: formData,
                        beforeSend: function() {
                            $('body').addClass('pointerEventsNone');
                            KTApp.blockPage({ overlayColor: '#000000', type: 'v2', state: 'primary', message: 'Processing File...' });
                        },
                        success: function(response) {
                            $('#tblTopRows').html(response);
                            $('.kt-select2').select2();
                            $('body').removeClass('pointerEventsNone');
                            KTApp.unblockPage();
                        },
                        error: function() {
                            thix.val('');
                            $('body').removeClass('pointerEventsNone');
                            KTApp.unblockPage();
                        }
                    });
                }
            });

            $('#tableName').on('change', function() {
                $('#tblTopRows').html('');
                $('#chooseFile').val('');
            });
        });
    </script>
@endsection