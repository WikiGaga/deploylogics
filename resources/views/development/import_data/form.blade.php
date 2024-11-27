@extends('layouts.template')
@section('title', 'Import Data')

@section('pageCSS')
    <style>
        .select2.select2-container{
            min-width: 170px !important;
        }
    </style>
@endsection

@section('content')
    @permission($data['permission'])
        <form id="import_data_form" class="erp_form_validation kt-form" method="post" enctype="multipart/form-data" action="{{ route('import.store') }}">
            @csrf
            <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                <div class="kt-portlet kt-portlet--mobile">
                    <div class="kt-portlet__head kt-portlet__head--lg">
                        @include('elements.page_header',['page_data' => $data['page_data']])
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        Import Data
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="form-group-block row">
                                            <label class="col-lg-5 erp-col-form-label" for="tableName">Select Database Table <span class="required">*</span></label>
                                            <div class="col-lg-7">  
                                                <div class="erp-select2 form-group">
                                                    <select name="table_name" id="tableName" class="form-control kt-select2 erp-form-control-sm select2-hidden-accessible">
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
                                            <label class="col-lg-5 erp-col-form-label" for="tableName">Select File <span class="required">*</span></label>
                                            <div class="col-lg-7">
                                                <input type="file" class="form-control-file" id="chooseFile" name="csv_file">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4" id="tblTopRows">
                                    
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endpermission
@endsection

@section('pageJS')
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/development/data-import.js') }}" type="text/javascript"></script>
    <script>
        $('#chooseFile').on('change',function(){
            var thix = $(this);
            var file = document.querySelector("#chooseFile").files[0];
            var name = file.name;
            var ext = name.split('.').pop().toLowerCase();
            var size = file.size||file.fileSize;
            if($.inArray(ext, ['csv']) == -1) 
            {
                toastr.error("Invalid File Selection (Allowed CSV)");
                thix.val('');
            }else if(size > 10000000){
                toastr.error("Fie size exceed (Max 10Mb)");
                thix.val('');
            }else{
                var formData = new FormData(document.querySelector("#import_data_form"));
                $.ajax({
                    headers : {
                        'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('import.selected') }}",
                    method : 'POST',
                    processData: false,
                    contentType: false,
                    cache:false,
                    data : formData,
                    beforeSend: function(){
                        $('body').addClass('pointerEventsNone');
                    },
                    success: function(response){
                        $('#tblTopRows').html(response);
                        $('.kt-select2').select2();
                        $('body').removeClass('pointerEventsNone');
                    },
                    error: function(){
                        thix.val('');
                        $('body').removeClass('pointerEventsNone');
                    }
                });
            }
        });

        $('#tableName').on('change',function(){
            $('#tblTopRows').html('');
            $('#chooseFile').val('');
        });
    </script>
@endsection


