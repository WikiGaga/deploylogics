<div class="kt-portlet__head-label">
    <span class="kt-portlet__head-icon">
        <i class="kt-font-brand flaticon2-file"></i>
    </span>
    <h3 class="kt-portlet__head-title">
        {{isset($header_data['title'])?$header_data['title']:""}}<small class="text-capitalize">{{isset($header_data['type'])?ucwords($header_data['type']):""}}</small>
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
                <i class="la la-long-arrow-left"></i> Back
            </a>
        @endif
    </div>
</div>
