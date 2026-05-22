<style>

    .stg-timeline:before {
        left: 11.65rem !important;
    }

    .stg-timeline .kt-timeline-v2__items .kt-timeline-v2__item .kt-timeline-v2__item-time {
        font-size: 12px !important;
        padding-top: 5px;
    }

    .stg-timeline .kt-timeline-v2__items .kt-timeline-v2__item .kt-timeline-v2__item-cricle {
        left: 10.9rem !important;
    }

    .stg-timeline .kt-timeline-v2__items .kt-timeline-v2__item .kt-timeline-v2__item-text {
        padding: 0.35rem 0 0 12rem !important;
    }

</style>
@php
    if (!isset($current_stg_activities) && isset($data['menu_dtl_id']) && isset($id)) {
        $current_stg_activities = \App\Models\TblStgFormLog::with('flow_dtl','action_btn_dtl','user','criteria_action')
            ->where(\App\Library\Utilities::currentBCB())
            ->where('menu_dtl_id', $data['menu_dtl_id'])->where('document_id', $id)->orderBy('created_at','desc')->get();
    }
    $current_stg_detail = null;
    if (isset($data['menu_dtl_id']) && isset($id)) {
        $current_stg_detail = \App\Models\TblStgFormLog::where('menu_dtl_id', $data['menu_dtl_id'])->where(\App\Library\Utilities::currentBCB())->where('document_id', $id)->where('posted', 0)->first();
    }
    $latest_remark = '';
    // if (isset($current_stg_activities) && $current_stg_activities->isNotEmpty()) {
    //     $latest_remark = $current_stg_activities->first()->remarks ?? '';
    // } elseif (isset($current_stg_detail) && isset($current_stg_detail->remarks)) {
    //     $latest_remark = $current_stg_detail->remarks;
    // }
@endphp
<div class="kt-portlet kt-portlet--mobile bottom_part">
    {{-- <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Staging History</h3>
        </div>
    </div> --}}
    <div class="kt-portlet__body">
        <div class="row">
            <div class="{{ isset($staging_activity_only) && $staging_activity_only ? 'col-lg-12' : 'col-lg-6' }}">
                @include('staging_activity.recent_activity')
            </div>
            @if(!isset($staging_activity_only) || !$staging_activity_only)
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-3 erp-col-form-label">Remarks:</label>
                    <div class="col-lg-9">
                        <textarea name="flow_remarks" id="flow_remarks" rows="3" class="form-control ">{{ $latest_remark }}</textarea>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
