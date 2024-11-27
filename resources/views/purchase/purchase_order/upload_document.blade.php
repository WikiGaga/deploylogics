<div class="modal-header" style="padding: 7px 10px;">
    <h5 class="modal-title" id="exampleModalLabel">Document Upload</h5>
    <button type="submit" id="submit_document" class="btn btn-primary btn-sm" style="margin-left: 20px">Upload</button>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>


<form id="upload_doc" method="post" action="/form-upload-document-files" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="hidden" name="form_id" value="{{isset($data['form_id'])?$data['form_id']:''}}">
    <input type="hidden" name="form_type" value="{{isset($data['form_type'])?$data['form_type']:''}}">
    <input type="hidden" name="menu_id" value="{{isset($data['menu_id'])?$data['menu_id']:''}}">
    <div class="modal-body">
        <style>
            .dropzone .dz-preview .dz-progress{
                top:10px;
            }
            .dz-image>img{
                width: 121px;
            }
            .dropzone .dz-preview.dz-image-preview {
                background: #fafafa;
            }
        </style>
        <script> var db_dropzone_imgs = []; var attachments = 0; var count = 0;</script>
        {{--{{dd($data['current']->toArray())}}--}}
        <div id="kt_repeater_6">
            <div data-repeater-list="document_list">
                @if(count($data['current']) != 0)
                    @php $count = 0; @endphp
                    @foreach($data['current'] as $current)
                        <div data-repeater-item class="document_items">
                            <div class="row">
                                <div class="col-lg-12 text-right">
                                    <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger">
                                        <i class="la la-trash-o"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row form-group-block">
                                <div class="col-lg-6">
                                    <div class="erp-page--title">
                                        {{isset($data['form_code'])?$data['form_code']:""}}
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group-block">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Documents Name: <span class="required">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="hidden">
                                            <input type="text" maxlength="100" name="doc_name" value="{{$current->document_upload_name}}" class="doc_name form-control erp-form-control-sm mustfill">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Documents Type:</label>
                                        <div class="col-lg-6">
                                            <div class="erp-select2">
                                                <select class="form-control kt-select2 erp-form-control-sm doc_type" name="doc_type">
                                                    <option value="0">Select</option>
                                                    @if(count($data['document_types']) != 0)
                                                        @foreach($data['document_types'] as $document_type)
                                                            <option value="{{$document_type->document_id}}" {{$document_type->document_default_value == 1?"selected":""}}>{{$document_type->document_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- /row --}}
                            <div class="row form-group-block">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Reference No:</label>
                                        <div class="col-lg-6">
                                            <input type="text" maxlength="100" name="reference_num" class="serial_num form-control erp-form-control-sm" value="{{ isset($current->document_refrence_number) ? $current->document_refrence_number : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Place of issue:</label>
                                        <div class="col-lg-6">
                                            <input type="text" maxlength="100" name="place_of_issue" class="place_of_issue form-control erp-form-control-sm" value="{{ isset($current->document_place_of_issue) ? $current->document_place_of_issue : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- /row --}}
                            <div class="row form-group-block">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Date of Issue:</label>
                                        <div class="col-lg-6">
                                            <div class="input-group date">
                                                <input type="text" name="issue_date" class="kt_datepicker issue_date form-control erp-form-control-sm c-date-p" readonly value="{{ date('d-m-Y' , strtotime($current->document_date_of_issue)) ?? date('d-m-Y') }}"/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Date of Expiry:</label>
                                        <div class="col-lg-6">
                                            <div class="input-group date">
                                                <input type="text" name="expiry_date" class="kt_datepicker expiry_date form-control erp-form-control-sm c-date-p" readonly value="{{ date('d-m-Y' , strtotime($current->document_date_of_expiry)) ?? date('d-m-Y') }}"/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <label class="col-lg-3 erp-col-form-label">Remarks:</label>
                                        <div class="col-lg-9">
                                            <input type="text" maxlength="250" value="{{$current->document_upload_remarks}}" name="remarks" class="remarks form-control erp-form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- /row --}}
                            <div class="row form-group-block">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <label class="col-lg-12 erp-col-form-label">
                                            Attach File:
                                            <span class="text-muted">Max file size is 10MB.</span>
                                        </label>

                                        <div class="col-lg-12 files">
                                            <div class="dropzone dropzone-default dropzone-brand kt_dropzone dz-clickable {{(count($current->files) == 0)?'':'dz-started'}}">
                                                <div class="dropzone-msg dz-message needsclick">
                                                    <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
                                                    <span class="dropzone-msg-desc">Upload up to 10 files</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- /row --}}
                            <hr style="border: 1px solid #607d8b; background: #607d8b;">
                            @php
                                $attachments = count($current->files);    
                            @endphp
                            <script>
                                attachments = '{{$attachments}}';
                            </script>
                            @foreach($current->files as $file)
                                <script>
                                    var db_files = [];
                                    db_files['path'] = "/user_documents/{{$file['document_upload_files_name']}}";
                                    db_files['dbMockFile'] = { id:{{$file['document_upload_files_id']}},name: '{{$file['document_upload_files_name']}}', size: '{{$file['document_upload_files_size']}}' };
                                    db_dropzone_imgs.push(['{{$count}}' , '{{$loop->iteration}}' , db_files]);
                                </script>
                            @endforeach
                        </div>
                    @php $count++; @endphp
                    @endforeach
                @else
                    {{--if no files--}}
                    <div data-repeater-item class="document_items">
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger">
                                    <i class="la la-trash-o"></i>
                                </a>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="erp-page--title">
                                    {{isset($data['form_code'])?$data['form_code']:""}}
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Documents Name: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" maxlength="100" name="doc_name" value="" class="doc_name form-control erp-form-control-sm mustfill">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Documents Type:</label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm doc_type" name="doc_type">
                                                <option value="0">Select</option>
                                                @if(count($data['document_types']) != 0)
                                                    @foreach($data['document_types'] as $document_type)
                                                        <option value="{{$document_type->document_id}}" {{$document_type->document_default_value == 1?"selected":""}}>{{$document_type->document_name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- /row --}}
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Serial No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" maxlength="100" name="serial_num" class="serial_num form-control erp-form-control-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Place of issue:</label>
                                    <div class="col-lg-6">
                                        <input type="text" maxlength="100" name="place_of_issue" class="place_of_issue form-control erp-form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- /row --}}
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Date of Issue:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group date">
                                            <input type="text" name="issue_date" class="kt_datepicker issue_date form-control erp-form-control-sm c-date-p" readonly value="{{date('d-m-Y')}}" />
                                            <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Date of Expiry:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group date">
                                            <input type="text" name="expiry_date" class="kt_datepicker expiry_date form-control erp-form-control-sm c-date-p" readonly value="{{date('d-m-Y')}}"/>
                                            <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">Remarks:</label>
                                    <div class="col-lg-9">
                                        <input type="text" maxlength="250" name="remarks" class="remarks form-control erp-form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- /row --}}
                        <div class="row form-group-block">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-12 erp-col-form-label">
                                        Attach File:
                                        <span class="text-muted">Max file size is 10MB.</span>
                                    </label>
                                    <div class="col-lg-12 files">
                                        <div class="dropzone dropzone-default dropzone-brand kt_dropzone">
                                            <div class="dropzone-msg dz-message needsclick">
                                                <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
                                                <span class="dropzone-msg-desc">Upload up to 10 files</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- /row --}}
                        <hr style="border: 1px solid #607d8b; background: #607d8b;">
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-lg-12 kt-align-right">
                    <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
                        <i class="fa fa-plus"></i> Add
                    </a>
                </div>
            </div>
        </div>

    </div>
</form>
<script>
    var cd = console.log;
</script>

<script>
    $(document).on('click','.dz-remove',function(){
        var thix = $(this);
        var dropzone = thix.parents('.dropzone');
        thix.parents('.dz-preview.dz-image-preview').remove();
        cd(dropzone.find('.dz-preview.dz-image-preview').length);
        if(dropzone.find('.dz-preview.dz-image-preview').length == 0){
            dropzone.removeClass('dz-started');
        }
    });
    var KTFormRepeater = function() {
        var kt_repeater_allowance = function() {
            $('#kt_repeater_6').repeater({
                initEmpty: false,
                isFirstItemUndeletable: true,
                defaultValues: {
                    // 'text-input': 'foo'
                },
                show: function() {
                    $('.kt-select2').select2(s2());
                    $(this).find('.kt_datepicker').datepicker(dp());
                    $(this).find('.kt_dropzone').dropzone(dz());
                    $(this).find('input[data-type="uploadedfile"]').remove();
                    $(this).slideDown();
                },
                ready: function (setIndexes) {
                    $('.kt-select2').select2(s2());
                },
                hide: function(deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            });
        }
        return {
            // public functions
            init: function() {
                kt_repeater_allowance();
            }
        };
    }();
    jQuery(document).ready(function() {
        KTFormRepeater.init();
    });
    $('.kt_datepicker').datepicker(dp());
    $('.kt_dropzone').empty();


    $('.kt_dropzone').dropzone(dz());
    
    function s2(){
        return {
            placeholder: "Select"
        }
    }
    function dp(){
        return {
            rtl: KTUtil.isRTL(),
            todayHighlight: true,
            format:'dd-mm-yyyy',
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            todayBtn:true
        }
    }
    function dz(){
        var url = "/form-upload-document-attach";
        var url_remove = "/remove-document-files";
        var form_id_val = $('#form_id').val();
        var form_type_val = $('#form_type').val();
        return {
            url: url, // Set the url for your upload script location
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 10,
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            autoProcessQueue: true,
            uploadMultiple: true,
            autoDiscover: true,
            parallelUploads: 10,
            acceptedFiles: 'image/*',
            dictRemoveFileConfirmation:  "Are you sure? You want to remove this file?",
            headers: {
                'x-csrf-token': $('meta[name="csrf-token"]').attr('content'),
            },
            accept: function(file, done) {
                var thix = $(this.element);
                var document_items = thix.parents('.document_items');
                cd(document_items);
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            },
            init: function (file) {
                var myDropzone = this;
                var thix = $(this.element);
                var document_items = thix.parents('.document_items');
                var index = document_items.index();
                myDropzone.on("success" , function(file , response){
                    document_items.append('<input type="hidden" data-type="uploadedfile" id="'+ file.upload.uuid +'" name="document_list['+index+'][files][]" value="'+ response +'" />');
                });
                myDropzone.on("removedfile", function(file) { 
                    if(file.current){
                        if(file.id !== undefined){
                            $('input[id="'+file.id+'"]').remove();
                            // $.ajax({
                            //     headers: {
                            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            //     },
                            //     type: "POST",
                            //     url: url_remove+'/'+file.id,
                            //     success: function(response,data) {
                            //         cd(response);
                            //         if(response.status == 'success'){
                            //             $('input[id="'+file.id+'"]').remove();
                            //             toastr.success(response.message);
                            //         }else{
                            //             toastr.error(response.message);
                            //         }
                            //         // hideModaldropZone()
                            //     },
                            //     error: function(response,status) {}
                            // });
                        }
                    }else{
                        $('#' + file.upload.uuid).remove();
                    }
                });
                if(db_dropzone_imgs.length != 0){
                    db_dropzone_imgs.forEach(function(item,index){
                        if(item[0] == count){
                            var mockFile = { id:item[2].dbMockFile.id, name: item[2].dbMockFile.name, value: item[2].dbMockFile.name, size: item[2].dbMockFile.size , current : true};
                            var filename = item[2].path;
                            var ext = filename.split('.').pop()
                            var imgExt = ['jpg','png','gif','webp','bmp','jpeg'];
                            // Seprate the DropZones
                            myDropzone.emit("addedfile", mockFile);
                            if(imgExt.includes(ext)){
                                myDropzone.emit("thumbnail", mockFile, item[2].path);
                            }
                            myDropzone.emit("complete", mockFile);
                        }    
                    });
                    count++;    
                }
            }
        };
    }
    $('#submit_document').click(function (e) {
        e.preventDefault();
        var form = $('#upload_doc');
        var url = form.attr('action');
        var validate = 1;
        var requiredImages = $('#upload_doc .document_items');
        requiredImages.each(function(index , element){
            var Images = $('.document_items')[index];
            var uploadedImages = Images.querySelectorAll('input[data-type="uploadedfile"]').length;
            console.log(uploadedImages);
            if(uploadedImages == 0){
                validate = 3;
            }
        });

        var requiredFields = $('.document_items .mustfill');
        requiredFields.each(function(index,element){
            if(element.value == "" || element.value == "0"){
                validate = 2;
            }
        });
        

        if(validate == 1){
            var formData = new FormData($('#upload_doc')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url         : url,
                type        : 'POST',
                dataType	: 'json',
                data        : formData,
                cache       : false,
                contentType : false,
                processData : false,
                beforeSend  : function(){
                    $('#submit_document').prop('disabled', true);
                },
                success: function(response,status) {
                    if(response.status == 'success'){
                        setTimeout(function () {
                            $('#submit_document').prop('disabled', false);
                        }, 2000);
                        toastr.success(response.message);
                        hideModaldropZone();
                    }
                },
                error: function(response,status) {
                    console.log(response);
                    console.log(response.responseJSON);
                    toastr.error(response.responseJSON.message);
                    setTimeout(function () {
                        $('#submit_document').prop('disabled', false);
                    }, 2000);
                },
            });
        }else if(validate == 2){
            toastr.error('Please fill all the required fields.');
        }else if(validate == 3){
            toastr.error('Please upload atleast one image with each document.');
        }else{
            toastr.error('Something went wrong! Try again later.');
        }
    });
    

    function hideModaldropZone(){
        $('#kt_modal_md').find('.modal-content').empty();
        $('#kt_modal_md').find('.modal-content').html(' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
        $('.modal').modal('hide');
    }


    // Putting the Alreary Uploaded Files in Input Fields
    $(document).ready(function(){
        if(db_dropzone_imgs.length != 0){
            db_dropzone_imgs.forEach(function(item,index){
                var node = document.createElement("input"); 
                node.setAttribute('data-type' , 'uploadedfile');
                node.setAttribute('name' , 'document_list['+item[0]+'][files][]');
                node.setAttribute('id' , item[2].dbMockFile.id);
                node.setAttribute('value' , item[2].dbMockFile.name);
                node.setAttribute('type' , 'hidden');
                document.querySelectorAll('.document_items')[item[0]].append(node);
            });
        }
    });
</script>


