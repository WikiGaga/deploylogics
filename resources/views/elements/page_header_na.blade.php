<div class="kt-portlet__head-label">
    <span class="kt-portlet__head-icon">
        <i class="kt-font-brand flaticon2-file"></i>
    </span>
    <h3 class="kt-portlet__head-title">
        @php
            $title = isset($page_data['title']) ? $page_data['title'] : '';
            $title_key = 'message.' . strtolower(str_replace([' ', '-'], '_', $title));
            $translated_title = __($title_key);
            $display_title = ($translated_title !== $title_key) ? $translated_title : $title;

            $type = isset($page_data['type']) ? $page_data['type'] : '';
            $type_key = 'message.' . strtolower($type);
            $translated_type = __($type_key);
            $display_type = ($translated_type !== $type_key) ? $translated_type : ucwords($type);
        @endphp
        {{ $display_title }}<small class="text-capitalize">{{ $display_type }}</small>
    </h3>
    <div class="erp-page--actions">
        @if(isset($page_data['action']) && $page_data['action'] != '')
            <button type="submit" class="btn btn-sm btn-success" id="grnUpdateNoAuth">{{$page_data['action']}}</button>
        @endif
        <a href='https://wa.me/' target='_blank' class="btn btn-sm btn-success btn-icon "><i style="font-size: 22px;" class="la la-whatsapp"></i></a>
    </div>
</div>
<div class="kt-portlet__head-toolbar">
    <div class="kt-portlet__head-wrapper">
        <div class="btn-group btn-group-sm switch-entry" role="group" aria-label="...">
            @if(isset($case) && $case == 'edit' && isset($form_type) && ($form_type == 'purc_order' || $form_type == 'bank_distribution' || $form_type == 'employee'))
            <a href="javascript:;" id="upload_documents" class="btn btn-sm btn-switch-entry" style="border-right: 3px solid #f0f8ff;"><i class="fa fa-upload"></i></a>
            @endif
        </div>
    </div>
</div>
