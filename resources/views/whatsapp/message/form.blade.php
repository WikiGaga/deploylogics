@extends('layouts.template')
@section('title', 'WhatsApp Message')

@section('pageCSS')
    <style>
        .dz-details .dz-size{
            display: none;
        }
        .dz-image img{
            width: 100%;
        }
        .dz-image{
            background: url('/assets/media/files/pdf.svg');
            cursor: pointer;
            background-blend-mode: luminosity;
            background-color: black;
            color: #fff;
        }
    </style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $current = [];
        }
        if($case == 'edit'){
            $id = $data['current']->msg_id;
            $name = $data['current']->remarks;
            $cmd_id = $data['current']->cmd_id;
            $remarks = $data['current']->remarks; 
            $status = trim($data['current']->msg_status);
            $details = isset($data['current']->dtls) ? $data['current']->dtls : [];
        }
    @endphp
    @permission($data['permission'])
    <form id="contact_group_form" class="master_form kt-form" method="post" action="{{ action('WhatsApp\WAMessagesController@store', isset($id)?$id:"") }}">
        <input type="hidden" id="form_type" value="wa_message">
    @csrf
    <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <!--begin::Form-->
                    <div class="kt-portlet__body">
                        <div class="form-group-block row">
                            <div class="col-lg-8">
                                <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">Message Type: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm" id="cmd_type" name="cmd_type" autocomplete="false">
                                                <option value="0">Select</option>
                                                @foreach($data['types'] as $type)
                                                    @php $type_id_var = isset($cmd_id)?$cmd_id:"" @endphp
                                                    <option value="{{$type->cmd_id}}" {{ $type_id_var == $type->cmd_id ? "selected" : "" }}>{{$type->remarks}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">Status: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @if($case == 'edit')
                                                    @php $entry_status = isset($status)?$status:""; @endphp
                                                    <input type="checkbox" name="is_active" {{$entry_status=="Y"?"checked":""}}>
                                                @else
                                                    <input type="checkbox" name="is_active" checked>
                                                @endif
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-md-12">
                                <div class="row">
                                    <label class="col-lg-2 erp-col-form-label">Remarks:<span class="required">*</span></label>
                                    <div class="col-lg-10">
                                        <input type="text" id="remarks" name="remarks" class="moveIndex form-control erp-form-control-sm" value="{{ isset($remarks) ? $remarks : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group-block mt-4 @if($case == 'new') d-none @endif" id="fields-container">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h4>Fields:</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row mt-3" id="fields">
                                    @if($case == 'edit')
                                        <script> var db_dropzone_imgs = []; var attachments = 0; var count = 0;</script>
                                        @foreach($data['parameters'] as $parameter)
                                            @if($parameter->field_type == "text")
                                                @foreach($details as $detail)
                                                    @if($parameter->par_id == $detail->par_id)
                                                        @php $par_value = $detail->par_value; break;@endphp
                                                    @else
                                                        @php $par_value = ""; @endphp
                                                    @endif
                                                @endforeach
                                                <div class="col-lg-6">
                                                    <div class="row form-group-block">
                                                        <label class="col-lg-12 erp-col-form-label">
                                                            {{ $parameter->remarks }}
                                                            @if($parameter->is_required == 'YES')
                                                                <span class="required">*</span>
                                                            @endif
                                                        </label>
                                                        <div class="col-lg-12">
                                                            <input type="text" id="{{ $parameter->par_name }}" name="pd[{{ $parameter->par_id }}]" class="moveIndex form-control erp-form-control-sm" value="{{ $par_value }}" />
                                                        </div>
                                                    </div>
                                                </div> 
                                            @endif
                                            @if($parameter->field_type == "file")
                                                @foreach($details as $detail)
                                                    @if($parameter->par_id == $detail->par_id)
                                                        @php 
                                                            $par_value = explode(',' , $detail->par_value); 
                                                            break;
                                                        @endphp
                                                    @else
                                                        @php $par_value = []; @endphp
                                                    @endif
                                                @endforeach
                                                <div class="col-lg-6">
                                                    <div class="row form-group-block">
                                                        <label class="col-lg-12 erp-col-form-label">
                                                            {{ $parameter->remarks }}
                                                            @if($parameter->is_required == 'YES')
                                                                <span class="required">*</span>
                                                            @endif
                                                        </label>
                                                        <div class="col-lg-12 files">
                                                            @foreach($par_value as $file)
                                                                <script>
                                                                    var db_files = [];
                                                                    db_files['path'] = '{{ $file }}';
                                                                    db_files['dbMockFile'] = { id:{{ $loop->iteration }} , name : '{{ $file }}' , value : '{{ $file }}' , size : '10450' };
                                                                    db_dropzone_imgs.push([1 , '{{$loop->iteration}}' , db_files]);
                                                                </script>
                                                                <input type="hidden" id="{{ $loop->iteration }}" name="pd[{{ $parameter->par_id }}][]" value="{{ $file }}">
                                                            @endforeach
                                                            <div class="dropzone dropzone-default dropzone-brand kt_dropzone dz-clickable" data-name="{{ $parameter->par_id }}">
                                                                <div class="dropzone-msg dz-message needsclick">
                                                                    <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
                                                                    <span class="dropzone-msg-desc">Upload up to 1 file</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($parameter->field_type == "textarea")
                                                @foreach($details as $detail)
                                                    @if($parameter->par_id == $detail->par_id)
                                                        @php $tarea_value = $detail->par_value; break;@endphp
                                                    @else
                                                        @php $tarea_value = ""; @endphp
                                                    @endif
                                                @endforeach
                                                <div class="col-lg-12">
                                                    <div class="row form-group-block">
                                                        <label class="col-lg-12 erp-col-form-label">
                                                            {{ $parameter->remarks }}
                                                            @if($parameter->is_required == 'YES')
                                                                <span class="required">*</span>
                                                            @endif
                                                        </label>
                                                        <div class="col-lg-12">
                                                            <textarea type="text" rows="4" value="{{ $tarea_value }}" id="{{ $parameter->par_name }}" maxlength="255" name="pd[{{ $parameter->par_id }}]" class="moveIndex form-control erp-form-control-sm">{{ $tarea_value }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>   
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
            </div>
        </div>
    </form>
    <!-- end:: Content -->
    @endpermission
@endsection
@section('pageJS')
        <script>
            $('#cmd_type').on('change' , function(e){
                e.preventDefault();
                var id = $(this).val();
                $.ajax({
                    headers : {
                        'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content') 
                    },
                    type:'POST',
                    url:'/wa-messages/getparmaters/'+id,
                    data:{},
                    beforeSend:function(){
                     $('body').addClass('pointerEventsNone');
                     $('#fields').html('');
                     $('#fields-container').addClass('d-none');
                    },
                    complete:function(){
                        $('body').removeClass('pointerEventsNone');
                    },
                    success: function(response, status){
                        $('body').removeClass('pointerEventsNone');
                        if(response.status == 'success'){
                            if(response.data.parameters.length > 0){
                                $('#fields-container').removeClass('d-none');
                                var fields = response.data.parameters;
                                var html = '';
                                fields.forEach((el,index)=>{
                                    var required = false;
                                    if(el.field_type == "text"){
                                        html += '<div class="col-lg-6">'+
                                            '<div class="row form-group-block">'+
                                                '<label class="col-lg-12 erp-col-form-label">'+el.remarks+':';
                                                    if(el.is_required == 'YES'){
                                                        var required = true;
                                                        html += '&nbsp;<span class="required">*</span>';
                                                    }
                                                html += '</label>'+
                                                '<div class="col-lg-12">'+
                                                    '<input type="text" id="'+el.par_name+'" name="pd['+el.par_id+']" class="moveIndex form-control erp-form-control-sm" />'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'; 
                                    }
                                    if(el.field_type == "file"){
                                        html += '<div class="col-lg-6">'+
                                                    '<div class="row">'+
                                                        '<label class="col-lg-12 erp-col-form-label">'+ el.remarks +':';
                                                        if(el.is_required == 'YES'){
                                                            html += '&nbsp;<span class="required">*</span>';
                                                        }
                                                        html += '</label>'+
                                                        '<div class="col-lg-12 files">'+
                                                            '<div class="dropzone dropzone-default dropzone-brand kt_dropzone dz-clickable" data-name="'+el.par_id+'">'+
                                                                '<div class="dropzone-msg dz-message needsclick">'+
                                                                    '<h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>'+
                                                                    '<span class="dropzone-msg-desc">Upload up to 1 file</span>'+
                                                                '</div>'+
                                                            '</div>'+
                                                        '</div>'+
                                                    '</div>'+
                                                '</div>';
                                    }
                                    if(el.field_type == "textarea"){
                                        html += '<div class="col-lg-12">'+
                                            '<div class="row form-group-block">'+
                                                '<label class="col-lg-12 erp-col-form-label">'+el.remarks+':';
                                                    if(el.is_required == 'YES'){
                                                        html += '&nbsp;<span class="required">*</span>'
                                                    }
                                                html += '</label>'+
                                                '<div class="col-lg-12">'+
                                                    '<textarea type="text" rows="4" id="sales_remarks" maxlength="255" name="pd['+el.par_id+']" class="moveIndex form-control erp-form-control-sm"></textarea>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>';   
                                    }
                                });
                                toastr.success('Fields are Loaded');
                                $('#fields').append(html);
                                $('.kt_dropzone').dropzone(dz());
                                // attachDropzone();
                            }else{
                                toastr.error('No Field To Show!');
                            }
                        }else{
                            toastr.error(response.message);
                        }
                    }
                });
            });
        </script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/wa-group.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/common/dropzone-custom.js') }}" type="text/javascript"></script>
    <script>
        if($('.kt_dropzone').length > 0){
            $('.kt_dropzone').empty();
            $('.kt_dropzone').dropzone(dz());
        }
        function dz(){
            var url = "{{ route('wa.whatsappAttachment') }}";
            var url_remove = "/remove-document-files";
            var form_id_val = $('#form_id').val();
            var form_type_val = $('#form_type').val();
            return {
                url: "{{ route('wa.whatsappAttachment') }}", // Set the url for your upload script location
                method : 'POST',
                paramName: "file", // The name that will be used to transfer the file
                maxFiles: 2,
                maxFilesize: 10, // MB
                addRemoveLinks: true,
                autoProcessQueue: true,
                uploadMultiple: true,
                autoDiscover: true,
                parallelUploads: 1,
                clickable: true,
                acceptedFiles: 'image/*,.pdf',
                dictRemoveFileConfirmation:  "Are you sure? You want to remove this file?",
                headers: {
                    'x-csrf-token': $('meta[name="csrf-token"]').attr('content'),
                },
                accept: function(file, done) {
                    var thix = $(this.element);
                    var document_items = thix.parents('.files');
                    done();
                },
                init: function (file) {
                    var myDropzone = this;
                    var thix = $(this.element);
                    var attachments = thix.parents('.files');
                    myDropzone.on("success" , function(file , response){
                        console.log(response);
                        var fieldName = thix.data('name');
                        attachments.append('<input type="hidden" data-type="uploadedfile" id="'+ file.upload.uuid +'" name="pd['+fieldName+'][]" value="'+ response +'" />');
                    });
                    myDropzone.on("removedfile", function(file) { 
                        if(file.current){
                            if(file.id !== undefined){
                                $('input[id="'+file.id+'"]').remove();
                            }
                        }else{
                            $('input#' + file.id).remove();
                        }
                    });
                    if(typeof db_dropzone_imgs !== 'undefined' && db_dropzone_imgs.length != 0){
                        db_dropzone_imgs.forEach(function(item,index){
                            name = item[2].dbMockFile.name.split('/').pop();
                            var mockFile = { id:item[2].dbMockFile.id, name: name, value: item[2].dbMockFile.value, size : item[2].dbMockFile.size, current : false};
                            var filename = item[2].path;
                            var ext = filename.split('.').pop();
                            var imgExt = ['jpg','png','gif','webp','bmp','jpeg','pdf'];
                            // Seprate the DropZones
                            myDropzone.emit("addedfile", mockFile);
                            if(imgExt.includes(ext)){
                                
                                myDropzone.emit("thumbnail", mockFile, item[2].path);
                                
                            }
                            myDropzone.emit("complete", mockFile); 
                        });
                        count++;    
                    }
                }
            };
        }

        $(document).on('click','.dz-image',function(e){
            e.preventDefault();
            var thix = $(this);
            var preview = thix.find('img');
            var src = preview.attr('src');
            
            window.open(src , 'blank');
        });
    </script>
@endsection
