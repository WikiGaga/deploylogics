<div class="modal-header" style="padding: 8px 18px;">
    <h5 class="modal-title">
        Notification
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="top: 10px;right: 10px;position: absolute;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form action="">
<div class="modal-body">
    <div class="row">
        <label class="col-lg-2 erp-col-form-label">Type:</label>
        <div class="col-lg-10">
            <div class="erp-select2 form-group">
                <select class="form-control tag-select2 erp-form-control-sm" multiple name="bypass_users[]">
                    <option value="1">SMS</option>
                    <option value="2">Email</option>
                    <option value="3">Whatsapp</option>
                    <option value="4">Notification</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <label class="col-lg-2 erp-col-form-label">Users:</label>
        <div class="col-lg-10">
            <div class="erp-select2 form-group">
                <select class="form-control tag-user2 erp-form-control-sm" multiple name="users[]">
                    <option value="1">Ehsan</option>
                    <option value="2">Ali</option>
                    <option value="3">Zaid</option>
                    <option value="4">Imran</option>
                    <option value="5">Khalid</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <label class="col-lg-2 erp-col-form-label">Message:</label>
        <div class="col-lg-10">
            <div class="summernote" id="kt_summernote_1"></div>
            {{--<textarea type="text" rows="5" name="notification_message" class="form-control erp-form-control-sm moveIndex"></textarea>--}}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success moveIndexSubmit moveIndex">Send</button>
</div>
</form>
<script>
    $('.tag-select2, #tag-select2_validate').select2({
        placeholder: "Add a type",
        tags: true
    });
    $('.tag-user2, #tag-select2_validate').select2({
        placeholder: "Add a users",
        tags: true
    });
    $('.summernote').summernote({
        height: 150
    });
</script>

