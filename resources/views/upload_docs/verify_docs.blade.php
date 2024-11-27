<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Verify Documents</h5>
    <button type="button" class="close" aria-label="Close"></button>
    </div>
    <div class="modal-body">

        @php
            $supported_image = ['gif', 'jpg', 'jpeg', 'png'];
        @endphp
        <div class="row ">
            @if(isset($data['current']->files) && count($data['current']->files) > 0)
                @foreach($data['current']->files as $files)
                    <div class="col-lg-4 text-center">
                        @php
                            $src_file_name = $files->document_upload_files_path;
                            $ext = strtolower(pathinfo($src_file_name, PATHINFO_EXTENSION));
                        @endphp
                        @if(in_array($ext, $supported_image))
                            <a href="/user_documents/{{$src_file_name}}" target="_blank">
                                <img src="/user_documents/{{$src_file_name}}" alt="" style="width:130px; height:130px;">
                            </a>
                        @else
                            {{--<a href="/user_documents/{{$src_file_name}}" target="_blank">
                                {{$ext}}
                            </a>--}}
                        @endif
                    </div>
                @endforeach
            @else
                Attachment not found...
            @endif
        </div>

</div>
