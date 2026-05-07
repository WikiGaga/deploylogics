@php
    $isViewOnly = (string) request('view', '') === '1';
    $stagingService = new \App\Services\StagingService();
    $currentFlowId = isset($data['flow_dtls']['current']) ? $data['flow_dtls']['current']->stg_flows_id : (isset($data['firstStag']) ? $data['firstStag'] : null);
    $formId = isset($id) ? $id : null;

    $actions = $stagingService->getFormActions($data['menu_dtl_id'], $currentFlowId, $formId);

    $actionsList = [];
    foreach ($actions as $k => $action) {
        $actionsList[$k]['id'] = $action->stg_actions_id;
        $actionsList[$k]['name'] = $action->stg_actions_name;
        $actionsList[$k]['label'] = $action->stg_actions_label;
    }

    // Get last flow ID for Post button logic
    $flows = $stagingService->getFormFlows($data['menu_dtl_id'], $currentFlowId, $formId);
    $lastFlowId = !empty($flows['all']) ? end($flows['all'])->stg_flows_id : null;
@endphp
@if(!$isViewOnly && isset($actionsList))
    @permission([$data['perPrefix'].'-create'])
    @if(!isset($id) && (in_array('save',array_column($actionsList, 'name')) || in_array('create',array_column($actionsList, 'name'))))
        @php
            $saveActionId = array_search('save', array_column($actionsList,'name', 'id'));
            if (!$saveActionId) {
                $saveActionId = array_search('create', array_column($actionsList,'name', 'id'));
            }
            $saveLabel = array_search('save', array_column($actionsList,'name','label'));
            if (!$saveLabel) {
                $saveLabel = array_search('create', array_column($actionsList,'name','label'));
            }
        @endphp
        <button type="submit" name="current_actions_id" value="{{$saveActionId}}" class="btn btn-sm btn-success">{{$saveLabel}}</button>
    @endif
    @endpermission
    @permission([$data['perPrefix'].'-edit'])
    @if(isset($id) && in_array('edit',array_column($actionsList, 'name')))
        <button type="submit" name="current_actions_id" value="{{array_search('edit', array_column($actionsList,'name', 'id'))}}" class="btn btn-sm btn-success">{{array_search('edit', array_column($actionsList,'name','label'))}}</button>
    @endif
    @endpermission
    @permission([$data['perPrefix'].'-back'])
    @if(in_array('back',array_column($actionsList, 'name')))
        <button type="submit" name="current_actions_id" value="{{array_search('back', array_column($actionsList,'name', 'id'))}}" class="btn btn-sm btn-danger">{{array_search('back', array_column($actionsList,'name','label'))}}</button>
    @endif
    @endpermission
    @permission([$data['perPrefix'].'-forward'])
    @if(in_array('forward',array_column($actionsList, 'name')))
        @if($currentFlowId == $lastFlowId)
            <button type="submit" name="current_actions_id" value="{{array_search('forward', array_column($actionsList,'name', 'id'))}}" class="btn btn-sm btn-info">Post</button>
        @else
            <button type="submit" name="current_actions_id" value="{{array_search('forward', array_column($actionsList,'name', 'id'))}}" class="btn btn-sm btn-info">{{array_search('forward', array_column($actionsList,'name','label'))}}</button>
        @endif
    @endif
    @endpermission
@endif
