<style>
    .stg_breadcrumb {
        text-align: center;
        display: inline-block;
        /*box-shadow: 0 2px 5px rgba(0,0,0,0.25);*/
        border-right: 1px solid #e91e63;
        overflow: hidden;
        border-radius: 5px;
        counter-reset: flag;
        margin: 0 5px;
    }
    .stg_breadcrumb__step::after {
        content: '';
        position: absolute;
        top: 0;
        right: -16px;
        width: 31px;
        height: 31px;
        transform: scale(0.707) rotate(45deg);
        z-index: 1;
        border-radius: 0 5px 0 50px;
        background:#fff;
        transition: background 0.5s;
        box-shadow: 2px -2px 0 2px #c70041;
    }
    .stg_breadcrumb__step--active::after {
        background: #e91e63;
    }

    .stg_breadcrumb__step::before {
        /* content: counter(flag);*/
        counter-increment: flag;
        border-radius: 100%;
        width: 20px;
        height: 20px;
        line-height: 20px;
        margin: 8px 0;
        position: absolute;
        top: 0;
        left: 30px;
        font-weight: bold;
        background: #fff;
        box-shadow: 0 0 0 1px #e91e63;
    }
    .stg_breadcrumb__step--active::before {
        color: #e91e63;
    }
    .stg_breadcrumb__step:first-child::before {
        left: 14px;
    }
    .stg_breadcrumb__step {
        text-decoration: none;
        outline: none;
        display: block;
        float: left;
        font-size: 12px;
        line-height: 30px;
        padding: 0 10px 0 30px;
        /*padding: 0 10px 0 60px;*/
        position: relative;
        background: #fff;
        color: #e91e63;
        transition: background 0.5s;
        border: 1px solid #e91e63;
    }
    .stg_breadcrumb__step--active {
        color: #fff;
        background: #e91e63;
    }

    .stg_breadcrumb__step:first-child {
        /*padding-left: 46px;*/
        padding-left: 10px;
        border-radius: 5px 0 0 5px;
    }
    .pointerEventsNone{
        opacity: 0.5;
        pointer-events: none;
        touch-action:none;
        user-select:none;
    }
    #dynamic_variable {
        /* position: relative;*/
    }
    .dynamic_variable_list {
        height: 176px;
        border: 2px solid #d6d6d6;
        padding: 10px;
        overflow: auto;
    }
    .dynamic_variable_list>div {
        padding: 2px 5px;
        color: #282a3c;
        font-weight: 400;
    }
    .dynamic_variable_list>div:hover {
        background-color: #f3f3f3;
        color: #fd397a;
        cursor: copy;
    }
    .dynamic_variable_copied {
        top: 2px;
        position: absolute;
        right: 12px;
        background: #fd397a;
        padding: 2px 15px;
        color: white;
    }
</style>
@php
    $flowsDataSrc = $staging_flow_dtls ?? $data['flow_dtls'] ?? null;
    if ($flowsDataSrc && isset($flowsDataSrc['all']) && count($flowsDataSrc['all']) > 0) {
        $flowsData = $flowsDataSrc;
    } else {
        $stagingService = new \App\Services\StagingService();
        $currentFlowId = isset($flowsDataSrc['current']) ? (is_object($flowsDataSrc['current']) ? $flowsDataSrc['current']->stg_flows_id : $flowsDataSrc['current']) : null;
        $formId = isset($id) ? $id : (isset($staging_form_id) ? $staging_form_id : null);
        $current = $data['current'] ?? null;
        $isAlreadyInStaging = $current && !empty($current->current_stg_id) && (int)($current->posted ?? 0) === 0;
        $stagingEnrolled = $current && isset($current->staging_apply) && (int) $current->staging_apply === 1;
        $skipCriteriaConditions = $isAlreadyInStaging || $stagingEnrolled;
        $flowsData = $stagingService->getFormFlows($data['menu_dtl_id'] ?? $staging_menu_dtl_id ?? null, $currentFlowId, $formId, $skipCriteriaConditions, $current);
    }
    $flows = $flowsData['all'] ?? [];
    $currentFlow = $flowsData['current'] ?? null;
    $currentFlowIdValue = $currentFlow ? (is_object($currentFlow) ? $currentFlow->stg_flows_id : $currentFlow) : null;
@endphp
<div class="stg_breadcrumb">
    @if(isset($flowsData['prev']) && $flowsData['prev'])
        <input type="hidden" name="prev_flow_id" value="{{ is_object($flowsData['prev']) ? $flowsData['prev']->stg_flows_id : $flowsData['prev'] }}">
    @endif
    <input type="hidden" name="current_flow_id" value="{{ $currentFlowIdValue }}">
    @if(isset($flowsData['next']) && $flowsData['next'])
        <input type="hidden" name="next_flow_id" value="{{ is_object($flowsData['next']) ? $flowsData['next']->stg_flows_id : $flowsData['next'] }}">
    @endif
    <input type="hidden" name="current_actions_id" id="staging_current_actions_id" value="">
    <input type="hidden" name="staging_action_code" id="staging_action_code" value="">
    @foreach($flows as $flow)
        <span class="stg_breadcrumb__step {{ $currentFlowIdValue == (is_object($flow) ? $flow->stg_flows_id : $flow) ? 'stg_breadcrumb__step--active' : '' }}">{{ is_object($flow) ? $flow->stg_flows_name : $flow }}</span>
    @endforeach
</div>
{{--<div class="stg_breadcrumb">
    <input type="hidden" name="current_flow_id" value="{{isset($data['stg']['flows']['current']->stg_flows_id)?$data['stg']['flows']['current']->stg_flows_id:$data['stg']['flows']['current']}}">
    <input type="hidden" name="next_flow_id" value="{{isset($data['stg']['flows']['next']->stg_flows_id)?$data['stg']['flows']['next']->stg_flows_id:$data['stg']['flows']['next']}}">
    @php $stg = true; @endphp
    @foreach($data['stg']['flows']['all'] as $flows)
        @php
            $current_stg_id = $data['stg']['flows']['current']->stg_flows_id;
            if($data['stg']['staging_apply'] == 1){
                $current_stg_id = $flows->stg_flows_id;
            }
        @endphp
        <span class="stg_breadcrumb__step {{ $current_stg_id == $flows->stg_flows_id ?" stg_breadcrumb__step--active":"" }}">{{$flows->stg_flows_name}}</span>
    @endforeach
</div>--}}
