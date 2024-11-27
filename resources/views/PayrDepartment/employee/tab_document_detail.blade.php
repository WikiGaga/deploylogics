@php
    if($case == 'new'){
        $document_type_id = 0;
    }
    if($case == 'edit'){
        $document_type_id = 0;
    }
@endphp
<div id="kt_repeater_6">
    <div data-repeater-list="document_list">
        <div data-repeater-item class="document_items">
            <div class="row">
                <div class="col-lg-12 text-right">
                    <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger">
                        <i class="la la-trash-o"></i>
                    </a>
                </div>
            </div>
            <div class="row form-group-block">
                <div class="col-lg-4">
                    <div class="row">
                        <label class="col-lg-6 erp-col-form-label">Documents Name:</label>
                        <div class="col-lg-6">
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" name="n">
                                    <option value="0">Select</option>
                                    @foreach($data['document_types'] as $document_type)
                                        <option value="{{$document_type->document_id}}" {{$document_type_id == $document_type->document_id?"selected":""}}>{{ucfirst(strtolower($document_type->document_name))}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <label class="col-lg-6 erp-col-form-label">Serial No:</label>
                        <div class="col-lg-6">
                            <input type="text" maxlength="100" name="no" class="form-control erp-form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <label class="col-lg-6 erp-col-form-label">Place of issue:</label>
                        <div class="col-lg-6">
                            <input type="text" maxlength="100" name="is" class="form-control erp-form-control-sm">
                        </div>
                    </div>
                </div>
            </div>{{-- /row --}}
            <div class="row form-group-block">
                <div class="col-lg-4">
                    <div class="row">
                        <label class="col-lg-6 erp-col-form-label">Date of Issue:</label>
                        <div class="col-lg-6">
                            <div class="input-group date">
                                <input type="text" name="da" class="kt_datepicker form-control erp-form-control-sm c-date-p" readonly/>
                                <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="la la-calendar"></i>
                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <label class="col-lg-6 erp-col-form-label">Date of Expiry:</label>
                        <div class="col-lg-6">
                            <div class="input-group date">
                                <input type="text" name="ex" class="kt_datepicker form-control erp-form-control-sm c-date-p" readonly/>
                                <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="la la-calendar"></i>
                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <label class="col-lg-6 erp-col-form-label">Remarks:</label>
                        <div class="col-lg-6">
                            <input type="text" maxlength="100" id="re" name="re" class="form-control erp-form-control-sm">
                        </div>
                    </div>
                </div>
            </div>{{-- /row --}}
            <div class="row form-group-block">
                <div class="col-lg-12">
                    <div class="row">
                        <label class="col-lg-2 erp-col-form-label">
                            Attach File:
                            <span class="form-text text-muted">Max file size is 1MB.</span>
                        </label>
                        <div class="col-lg-10 files">
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
                                            <div class="progress">
                                                <div class="progress-bar kt-bg-brand" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress></div>
                                            </div>
                                        </div>
                                        <div class="dropzone-toolbar">
                                            <span class="dropzone-start"><i class="flaticon2-arrow"></i></span>
                                            <span class="dropzone-cancel" data-dz-remove style="display: none;"><i class="flaticon2-cross"></i></span>
                                            <span class="dropzone-delete" data-dz-remove><i class="flaticon2-cross"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- /row --}}
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 kt-align-right">
            <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
                Add
            </a>
        </div>
    </div>
</div>


