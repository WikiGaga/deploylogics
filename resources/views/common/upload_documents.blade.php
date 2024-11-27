<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Upload Documents
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="row form-group-block">
            <label class="col-lg-3 erp-col-form-label">Documents Name:</label>
            <div class="col-lg-3">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" name="documents_name">
                        <option value="0">Select</option>
                        <option value="1">Wedding</option>
                        <option value="2">Sick</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row form-group-block">
            <label class="col-lg-3 erp-col-form-label">
                Attach File:
                <span class="form-text text-muted">Max file size is 1MB.</span>
            </label>
            <div class="col-lg-9 files">
                @if(count($data['files']) != 0)
                    <h6>
                        Upload Files
                    </h6>
                    @foreach($data['files'] as $files)
                        <a target="_blank" href="/documents-upload/{{$files->file_upload_path}}">{{$files->file_upload_name}}</a><br>
                    @endforeach
                    <hr>
                @endif


                <div class="dropzone dropzone-multi" id="kt_dropzone_4">
                    <div class="dropzone-panel">
                        <a class="dropzone-select btn btn-label-brand btn-bold btn-sm">Attach files</a>
                        <a class="dropzone-upload btn btn-label-brand btn-bold btn-sm">Upload All</a>
                        <a class="dropzone-remove-all btn btn-label-brand btn-bold btn-sm">Remove All</a>
                    </div>
                    <div class="dropzone-items">
                        <div class="dropzone-item" style="display:none">
                            <div class="dropzone-file">
                                <div class="dropzone-filename" title="some_image_file_name.jpg"><span data-dz-name>some_image_file_name.jpg</span> <strong>(<span  data-dz-size>340kb</span>)</strong></div>
                                <div class="dropzone-error" data-dz-errormessage></div>
                            </div>
                            <div class="dropzone-progress">
                                {{--<div class="progress">
                                    <div class="progress-bar kt-bg-brand" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress></div>
                                </div>--}}
                            </div>
                            <div class="dropzone-toolbar">
                                {{--<span class="dropzone-start"><i class="flaticon2-arrow"></i></span>--}}
                                <span class="dropzone-cancel" data-dz-remove style="display: none;"><i class="flaticon2-cross"></i></span>
                                <span class="dropzone-delete" data-dz-remove><i class="flaticon2-cross"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@section('pageJS')
@endsection
@section('customJS2')

    <script src="{{ asset('js/pages/js/common/dropzone-custom.js') }}" type="text/javascript"></script>

    <script>

    </script>
@endsection
