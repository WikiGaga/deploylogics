
<div class="kt-portlet__head-label">
    <span class="kt-portlet__head-icon">
        <i class="kt-font-brand flaticon2-file"></i>
    </span>
    <h3 class="kt-portlet__head-title">
        {{isset($page_data['title'])?$page_data['title']:""}}<small class="text-capitalize">{{isset($page_data['type'])?ucwords($page_data['type']):""}}</small>
    </h3>
    <div class="erp-page--actions">
        @if(isset($page_data['action']) && $page_data['action'] != '')
            <button type="submit" id="btn-update-entry" class="btn btn-sm btn-success">{{$page_data['action']}}</button>
        @endif
        @if(isset($data['page_data']['create']) && $data['page_data']['create'] != '')
            <a href="{{$data['page_data']['create']}}" class="btn btn-sm btn-brand btn-icon" id="btn-new-entry" title="Create New">
                <i class="la la-plus"></i>
            </a>
        @endif
        {{--<a href='https://wa.me/' target='_blank' class="btn btn-sm btn-success btn-icon "><i style="font-size: 22px;" class="la la-whatsapp"></i></a>--}}
        @if(isset($data['page_data']['print']) && $data['page_data']['print'] != '')
            <a href="" onclick="window.open( '{{$data['page_data']["print"]}}', 'print' ); return false" class="btn btn-sm btn-warning btn-icon" title="Print">
                <i class="la la-print"></i>
            </a>
        @endif
        {{--@if(isset($data['page_data']['pending_pr']) && $data['page_data']['pending_pr'] != '')
            <a href="#" class="btn btn-sm btn-primary btn-icon" id="openPendingPRBySupplier" title="Pending Purchase Return">
                <i class="la la-clipboard"></i>
            </a>
        @endif--}}
        @if(isset($data['page_data']['post']) && $data['page_data']['post'] != '')
            <a href="" onclick="voucher_posted();" style="background-color:#2471A3;color:#FFFF;" class="btn btn-sm btn-icon" title="Post">
                Post
            </a>
        @endif
    </div>
</div>
<div class="kt-portlet__head-toolbar">
    <div class="kt-portlet__head-wrapper">
        <div class="btn-group btn-group-sm switch-entry" role="group" aria-label="...">
            @php
                $upload_doc_allow = ['jv','pve','pv','cpv','crv','lv','brpv','purc_order','grn'];
                //dd($form_type);
            @endphp
            @if(isset($case) && $case == 'edit' && isset($form_type) && in_array($form_type,$upload_doc_allow))
                <a href="javascript:;" id="upload_documents" class="btn btn-sm btn-switch-entry" style="background-color:orange;color:#FFFF;" style="border-right: 3px solid #f0f8ff;"><i class="fa fa-upload"></i></a>
            @endif
            @if(isset($case) && $case == 'edit' && isset($page_data['log_print']) && ($page_data['log_print'] == true))
            <a href="javascript:;" id="log_print" class="btn btn-sm btn-switch-entry" style="border-right: 3px solid #f0f8ff;"><i class="fa fa-history"></i></a>
            @endif
            @if(isset($data['switch_entry']) && count($data['switch_entry']) != 0)
            <a href="{{isset($data['switch_entry']['first'])?$data['switch_entry']['first']:"javascript:;"}}" class="btn btn-sm btn-switch-entry"><i class="fa fa-step-backward"></i></a>
            <a href="{{isset($data['switch_entry']['prev'])?$data['switch_entry']['prev']:"javascript:;"}}" class="btn btn-sm btn-switch-entry"><i class="fa fa-arrow-circle-left"></i></a>
            <a href="{{isset($data['switch_entry']['next'])?$data['switch_entry']['next']:"javascript:;"}}" class="btn btn-sm btn-switch-entry"><i class="fa fa-arrow-circle-right"></i></a>
            <a href="{{isset($data['switch_entry']['last'])?$data['switch_entry']['last']:"javascript:;"}}" class="btn btn-sm btn-switch-entry"><i class="fa fa-step-forward"></i></a>
            @endif
            @if(isset($page_data['path_index']) && $page_data['path_index'] != "")
                @php $classes = isset($page_data['back_btn_classes'])?$page_data['back_btn_classes']:"" @endphp
                <a href="{{$page_data['path_index']}}" id="btn-back" class="btn btn-switch-entry btn-icon-sm back {{$classes}}">
                    Back
                </a>
                {{-- @else
                    <a href="/data/{{strtolower(str_replace(' ', '-', $page_data['page_title']))}}" class="btn btn-clean btn-icon-sm">
                        <i class="la la-long-arrow-left"></i> Back
                    </a>--}}
            @endif
        </div>
    </div>
</div>

