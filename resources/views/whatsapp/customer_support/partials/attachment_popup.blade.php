<!-- Attachment Modal -->
<div class="modal fade" id="attachmentPopup" tabindex="-1" role="dialog" aria-labelledby="attachmentPopupLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
        <form action="#" method="POST" id="whatsapp_attachment__form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attachmentPopupLabel">Send Attachment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="attachFileName">File Name <span class="required">*</span></label>
                        <input type="text" id="attachFileName" name="attachFileName" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6">
                        <label for="attachFileCaption">File Caption  <span class="required">*</span></label>
                        <input type="text" id="attachFileCaption" name="attachFileCaption" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row form-group-block attachment_files">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-12 erp-col-form-label">
                                        Attach File:
                                        <span class="text-muted">Max file size is 10MB.</span>
                                    </label>
                                    <div class="col-lg-12 files">
                                        <div class="dropzone dropzone-default dropzone-brand kt_dropzone dz-clickable">
                                            <div class="dropzone-msg dz-message needsclick">
                                                <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
                                                <span class="dropzone-msg-desc">Upload up to 10 files</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- /row --}}
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary send_whatsapp__attachment">Send Attachment</button>
                </div>
            </div>
        </form>
  </div>
</div>

<!-- Location Modal -->
<div class="modal fade" id="locationPopup" tabindex="-1" role="dialog" aria-labelledby="locationPopupLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="locationPopupLabel">Send Location</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="{{ action('WhatsApp\WhatsAppChatController@storeLocation') }}" method="POST" id="whatsAppLocationForm">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label for="">Lat <span class="required">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="waLocationLat" id="waLocationLat">
                    </div>
                    <div class="col-md-2">
                        <label for="">Lng <span class="required">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="waLocationLng" id="waLocationLng">
                    </div>
                    <div class="col-md-3">
                        <label for="">Name <span class="required">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="waLocationName" id="waLocationName">
                    </div>
                    <div class="col-md-4">
                        <label for="">Address <span class="required">*</span></label> 
                        <input type="text" class="form-control form-control-sm" name="waLocationAddress" id="waLocationAddress"> 
                    </div>
                    <div class="col-md-1 text-right">
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm storeWhatsAppLocation">
                            <i class="fa fa-plus p-0"></i>
                        </a>
                    </div>
                </div>
            </form>
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5>Stored Location</h5>
                </div>
            </div>
            <div id="storedLocations">
                @if(isset($data['locations']) && count($data['locations']) > 0)
                    @foreach($data['locations'] as $location)
                        <div class="row mb-2">
                            <div class="col-md-2">
                                <input type="text" class="form-control form-control-sm" disabled value="{{ $location->location_lat }}">
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control form-control-sm" disabled value="{{ $location->location_lng }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" disabled value="{{ $location->location_name }}">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm" disabled value="{{ $location->location_address }}">
                            </div>
                            <div class="col-md-1">
                                <a href="javascript:void(0);" class="btn btn-success btn-sm sendWhatsAppLocation" data-lat="{{ $location->location_lat }}" data-lng="{{ $location->location_lng }}" data-name="{{ $location->location_name }}" data-address="{{ $location->location_address }}">
                                    <i class="fa fa-paper-plane"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="row" id="noLocationFound">
                        <div class="col-md-12 text-center">
                            <i>No Location Found</i>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>