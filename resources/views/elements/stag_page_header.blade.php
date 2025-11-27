<div class="kt-portlet__head-label">
    <span class="kt-portlet__head-icon">
        <i class="kt-font-brand flaticon2-file"></i>
    </span>
    <h3 class="kt-portlet__head-title">
        @php
            $title = isset($header_data['title']) ? $header_data['title'] : '';
            $title_key = 'message.' . strtolower(str_replace([' ', '-'], '_', $title));
            $translated_title = __($title_key);
            $display_title = ($translated_title !== $title_key) ? $translated_title : $title;
            
            $type = isset($header_data['type']) ? $header_data['type'] : '';
            $type_key = 'message.' . strtolower($type);
            $translated_type = __($type_key);
            $display_type = ($translated_type !== $type_key) ? $translated_type : ucwords($type);
        @endphp
        {{ $display_title }}<small class="text-capitalize">{{ $display_type }}</small>
    </h3>
    <div class="erp-page--actions">
        @include('staging_activity.action_btns')
    </div>
</div>
<div class="kt-portlet__head-toolbar">
    <div class="kt-portlet__head-wrapper">
        @include('staging_activity.breadcrumb')
        @if(isset($header_data['path_index']))
            @php $classes = isset($header_data['back_btn_classes'])?$header_data['back_btn_classes']:"" @endphp
            <a href="{{$header_data['path_index']}}" id="btn-back" class="btn btn-clean btn-sm btn-icon-sm back {{$classes}}">
                <i class="la la-long-arrow-left"></i> {{ __('message.back') }}
            </a>
        @endif
    </div>
</div>
