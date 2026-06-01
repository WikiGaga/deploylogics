<style>
    .btn-custom {
        width: 4.5rem !important;
    }
</style>
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
    <div class="erp-page--actions {{ (isset($staging_data) && $staging_data['has_staging']) ? 'erp-page--actions-staging' : '' }}">

        @if(isset($data['current']) && (!empty($id) || !empty($data['id'] ?? null)))
            <input type="hidden" name="document_posted_state" id="document_posted_state" value="{{ (int) ($data['current']->posted ?? 0) }}">
            @if(!empty($data['current']->current_stg_id))
                <input type="hidden" name="document_current_stg_id" id="document_current_stg_id" value="{{ $data['current']->current_stg_id }}">
            @endif
        @endif
        @if(isset($data['page_data']['create']) && $data['page_data']['create'] != '')
            <a href="{{$data['page_data']['create']}}" class="btn btn-sm btn-brand btn-icon" id="btn-new-entry" title="{{ __('message.create_new') }}">
                <i class="la la-plus"></i>
            </a>
        @endif
        {{--<a href='https://wa.me/' target='_blank' class="btn btn-sm btn-success btn-icon "><i style="font-size: 22px;" class="la la-whatsapp"></i></a>--}}
        @if(isset($data['page_data']['print']) && $data['page_data']['print'] != '')
            <a href="" onclick="window.open( '{{$data['page_data']["print"]}}', 'print' ); return false" class="btn btn-sm btn-warning btn-icon" title="{{ __('message.print') }}">
                <i class="la la-print"></i>
            </a>
        @endif
        {{--@if(isset($data['page_data']['pending_pr']) && $data['page_data']['pending_pr'] != '')
            <a href="#" class="btn btn-sm btn-primary btn-icon" id="openPendingPRBySupplier" title="Pending Purchase Return">
                <i class="la la-clipboard"></i>
            </a>
        @endif--}}
        @if(!(isset($staging_data) && $staging_data['has_staging']))
            @php
                $pageData = $page_data ?? ($data['page_data'] ?? []);
                $postMenuId = $data['menu_dtl_id'] ?? $data['menu_id'] ?? $data['stock_menu_id'] ?? null;
                $postPerm = $postMenuId ? ($postMenuId . '-post') : null;
                $unpostPerm = $postMenuId ? ($postMenuId . '-un_post_module') : null;
                $cancelPerm = $postMenuId ? ($postMenuId . '-cancel') : null;
                $umPostedState = isset($data['current']->posted) ? (int) $data['current']->posted : 0;
                if ($umPostedState === 0 && !empty($pageData['is_posted'])) {
                    $umPostedState = 1;
                }
                if ($umPostedState === 0 && !empty($pageData['is_canceled'])) {
                    $umPostedState = 2;
                }
                $isPostedUm = $umPostedState === 1;
                $isCanceledUm = $umPostedState === 2;
                $isDraftUm = $umPostedState === 0;
                $cancelUrl = $pageData['cancel'] ?? ($data['page_data']['cancel'] ?? '');
                $postUrl = $pageData['post'] ?? ($data['page_data']['post'] ?? '');
                $canUmCancel = $cancelPerm && auth()->check() && auth()->user()->isAbleTo($cancelPerm);
            @endphp
            @if($postUrl != '')
                @if($postPerm && $unpostPerm)
                    @if($isPostedUm || $isCanceledUm)
                        @permission($unpostPerm)
                            <a href="" onclick="voucher_unposted(); return false;" style="background-color:#E74C3C;color:#FFFF;" class="btn btn-sm btn-icon btn-custom" title="{{ __('message.unpost') }}">
                                {{ __('message.unpost') }}
                            </a>
                        @endpermission
                    @elseif($isDraftUm)
                        @permission($postPerm)
                            <a href="" onclick="voucher_posted(); return false;" style="background-color:#2471A3;color:#FFFF;" class="btn btn-sm btn-icon btn-custom" title="{{ __('message.post') }}">
                                {{ __('message.post') }}
                            </a>
                        @endpermission
                        @if($canUmCancel && $cancelUrl != '')
                            <a href="" onclick="voucher_cancel(); return false;" style="background-color:#C0392B;color:#FFFF;" class="btn btn-sm btn-icon btn-custom" title="{{ __('message.cancel') }}">
                                {{ __('message.cancel') }}
                            </a>
                        @endif
                    @endif
                @endif
            @endif
        @endif
        {{-- @if($data['form_type'] == 'pos-sales-invoice') --}}
            <font class="tTip" color="" title="{{ __('message.send_via_whatsapp') }}">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-success padding-3 margin-0 dropdown-toggle btn-sm" id="whatsappmessagebtn" data-toggle="dropdown" aria-expanded="false">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="15" fill="currentColor" viewBox="0 0 32 32">
    <path d="M16.02 0C7.183 0 .042 7.143 0 15.962a15.93 15.93 0 0 0 2.6 8.735L.062 32l7.479-2.49a16.056 16.056 0 0 0 8.48 2.19H16C24.82 31.7 32 24.556 32 15.78A15.985 15.985 0 0 0 16.02 0zm8.934 23.42c-.371.98-1.89 1.813-3.106 2.01-.824.137-1.866.248-5.422-1.162a19.12 19.12 0 0 1-8.935-7.88 10.022 10.022 0 0 1-2.087-4.986c-.35-2.31.927-3.404 1.717-3.472a4.85 4.85 0 0 1 1.185.152c.37.093.876-.223 1.38.986.49 1.165 1.247 2.883 1.355 3.092.112.222.183.49.037.79-.14.296-.209.478-.414.74-.206.258-.441.577-.63.777-.208.222-.425.463-.183.878a13.31 13.31 0 0 0 2.425 3.063 12.327 12.327 0 0 0 3.918 2.487c.488.223.868.194 1.191-.06.37-.31 1.563-1.811 1.99-2.437.27-.371.524-.31.896-.185.37.12 2.408 1.14 2.822 1.346.415.207.684.308.79.48.115.185.115 1.054-.255 2.033z"/>
    </svg> --}}
    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" style="width:24px;height:22px;">
                        <span class="caret"></span>

                        {{-- @php
                        $message_sent =  getvalue("SELECT Entry_Code FROM whatsapp_log where Entry_Code = '".$_REQUEST['major']."' ");
                        @endphp
                        @if($message_sent)
                        <i class="icon wb-check" aria-hidden="true"></i>
                        @endif --}}

                    </button>
                    <ul class="dropdown-menu pull-right" aria-labelledby="exampleIconDropdown1" role="menu">
                        <li role="presentation"><a href="javascript:void(0)" role="menuitem" onclick="sendWhatsAppMessage()">{{ __('message.send') }}</a></li>
                    </ul>
                </div>
            </font>
        {{-- @endif --}}

        {{-- Staging action buttons (if staging is enabled) --}}
        @if(isset($staging_data) && $staging_data['has_staging'])
            @include('staging_activity.header_action_btns')
        @elseif(isset($page_data['action']) && $page_data['action'] != '')
            @php
                $umDocPostedState = isset($data['current']->posted) ? (int) $data['current']->posted : 0;
            @endphp
            @if($umDocPostedState === 0)
                <button type="submit" id="btn-update-entry" class="btn btn-sm btn-success">{{$page_data['action']}}</button>
            @endif
        @endif

    </div>
</div>
<div class="kt-portlet__head-toolbar">
    <div class="kt-portlet__head-wrapper">
        @php
            $showUmDocStatus = !(isset($staging_data) && $staging_data['has_staging'])
                && isset($data['current'])
                && isset($data['current']->posted)
                && isset($data['page_data']['post']);
            $umPostedVal = $showUmDocStatus ? (int) ($data['current']->posted ?? 0) : null;
        @endphp
        @if($showUmDocStatus && isset($id))
            @if($umPostedVal === 2)
                <span class="btn btn-sm btn-danger" style="cursor: default; pointer-events: none; margin-right: 8px;">
                    <i class="la la-ban"></i> Canceled
                </span>
            @elseif($umPostedVal === 1)
                <span class="btn btn-sm btn-success" style="cursor: default; pointer-events: none; margin-right: 8px;">
                    <i class="la la-check-circle"></i> Posted
                </span>
            @else
                <span class="btn btn-sm btn-info" style="cursor: default; pointer-events: none; margin-right: 8px;">
                    <i class="la la-file-alt"></i> Draft
                </span>
            @endif
        @endif
        <div class="btn-group btn-group-sm switch-entry" role="group" aria-label="...">
            @php
                $upload_doc_allow = ['jv','pve','pv','cpv','crv','lv','brpv','purc_order','grn'];
                //dd($form_type);
            @endphp
            @if(isset($case) && $case == 'edit' && isset($form_type) && in_array($form_type,$upload_doc_allow))
                <a href="javascript:;" id="upload_documents" class="btn btn-sm btn-switch-entry" style="background-color:orange;color:#FFFF;" title="{{ __('message.upload_documents') }}" style="border-right: 3px solid #f0f8ff;"><i class="fa fa-upload"></i></a>
            @endif
            @if(isset($case) && $case == 'edit' && isset($page_data['log_print']) && ($page_data['log_print'] == true))
            <a href="javascript:;" id="log_print" class="btn btn-sm btn-switch-entry" title="{{ __('message.history') }}" style="border-right: 3px solid #f0f8ff;"><i class="fa fa-history"></i></a>
            @endif
            @if(isset($data['switch_entry']) && count($data['switch_entry']) != 0)
            <a href="{{isset($data['switch_entry']['first'])?$data['switch_entry']['first']:"javascript:;"}}" class="btn btn-sm btn-switch-entry" title="{{ __('message.first') }}"><i class="fa fa-step-backward"></i></a>
            <a href="{{isset($data['switch_entry']['prev'])?$data['switch_entry']['prev']:"javascript:;"}}" class="btn btn-sm btn-switch-entry" title="{{ __('message.previous') }}"><i class="fa fa-arrow-circle-left"></i></a>
            <a href="{{isset($data['switch_entry']['next'])?$data['switch_entry']['next']:"javascript:;"}}" class="btn btn-sm btn-switch-entry" title="{{ __('message.next') }}"><i class="fa fa-arrow-circle-right"></i></a>
            <a href="{{isset($data['switch_entry']['last'])?$data['switch_entry']['last']:"javascript:;"}}" class="btn btn-sm btn-switch-entry" title="{{ __('message.last') }}"><i class="fa fa-step-forward"></i></a>
            @endif
            @if(isset($page_data['path_index']) && $page_data['path_index'] != "")
                @php $classes = isset($page_data['back_btn_classes'])?$page_data['back_btn_classes']:"" @endphp
                <a href="{{$page_data['path_index']}}" id="btn-back" class="btn btn-switch-entry btn-icon-sm back {{$classes}}">
                    {{ __('message.back') }}
                </a>
                {{-- @else
                    <a href="/data/{{strtolower(str_replace(' ', '-', $page_data['page_title']))}}" class="btn btn-clean btn-icon-sm">
                        <i class="la la-long-arrow-left"></i> Back
                    </a>--}}
            @endif
        </div>
    </div>
</div>

@php
    $pageDataForCancel = $page_data ?? ($data['page_data'] ?? []);
@endphp
@if(!(isset($staging_data) && $staging_data['has_staging']) && !empty($pageDataForCancel['cancel']))
<script>
function voucher_cancel() {
    if (!confirm('{{ __('message.cancel') }} this document?')) {
        return;
    }
    var fieldId = '{{ $pageDataForCancel['document_id_field'] ?? ($data['page_data']['document_id_field'] ?? 'form_id') }}';
    var docId = $('#' + fieldId).val() || $('#form_id').val() || $('#voucher_id').val();
    if (!docId) {
        toastr.error('Document id not found');
        return;
    }
    var payload = {};
    payload[fieldId] = docId;
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        url: '{{ $pageDataForCancel['cancel'] }}',
        data: payload,
        success: function(response, textStatus, xhr) {
            erpDocumentAjaxDone(response, xhr, { successMsg: '{{ __('message.cancel') }}' });
        },
        error: function(xhr) {
            erpDocumentAjaxDone(null, xhr, { errorMsg: 'Request failed' });
        }
    });
}
</script>
@endif

<script>
function formatPakPhoneNumber(phone) {
    phone = phone.replace(/[^0-9]/g, '');
    if (phone.startsWith('0')) {
        phone = '+92' + phone.slice(1);
    } else {
        phone = '+92' + phone;
    }
    return phone;
}

function formatOmanPhoneNumber(phone) {
    phone = phone.replace(/[^0-9]/g, '');
    phone = '+968' + phone;
    return phone;
}

</script>

