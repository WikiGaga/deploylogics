
@php
    $currentFlowId = isset($staging_data['flows']['current']) && is_object($staging_data['flows']['current'])
        ? $staging_data['flows']['current']->stg_flows_id
        : null;
    $actions = $staging_data['actions'] ?? [];
    $userHasAccess = (bool) ($staging_data['user_access'] ?? false);

    $isPosted = false;
    $isCompleted = false;
    $isCanceled = false;
    if (isset($data['current'])) {
        if (isset($data['current']->posted) && $data['current']->posted == 1) {
            $isPosted = true;
        }
        if (isset($data['current']->posted) && $data['current']->posted == 2) {
            $isCanceled = true;
        }
        if (isset($data['current']->staging_apply) && $data['current']->staging_apply == 1) {
            $isCompleted = true;
        }
    }
@endphp

<div class="stg-header-row" style="display: flex; justify-content: space-between; align-items: center; flex: 1; min-width: 0; gap: 8px;">
    <div class="stg-actions-left">
@if(!$isCanceled && !($isPosted && $isCompleted) && !empty($actions))
    @if(!$userHasAccess && isset($id))
        <button
            type="button"
            class="btn btn-sm btn-success stg-action-disabled"
            title="Update"
            data-stg-not-allowed="{{ __('message.staging_no_access') }}"
            onclick="(function(el){var msg=el.getAttribute('data-stg-not-allowed')||'Not allowed';if(window.toastr&&typeof window.toastr.error==='function'){window.toastr.error(msg);}else{alert(msg);}})(this); return false;">
            Update
        </button>
    @endif

    @foreach($actions as $action)
        @php
            $actionName = $action->stg_actions_name;
            $originalAction = $action->original_action ?? $actionName;
            $actionId = $action->stg_actions_id;

            $btnClass = 'btn-info';
            $finalLabel = '';
            $showButton = true;

            if ($actionName == 'save' || $originalAction == 'save' || $actionName == 'create' || $originalAction == 'create') {
                $btnClass = 'btn-success';
                $finalLabel = isset($id) ? 'Update' : 'Save';
            } elseif ($actionName == 'edit' || $originalAction == 'edit') {
                $btnClass = 'btn-success';
                $finalLabel = 'Update';
                if (!isset($id)) {
                    $showButton = false;
                }
            } elseif ($originalAction == 'back') {
                $btnClass = 'btn-danger';
                $finalLabel = 'Send Back';
                if (!isset($id)) {
                    $showButton = false;
                }
            } elseif ($originalAction == 'forward') {
                $btnClass = 'btn-info';
                $finalLabel = 'Forward';
                if (!isset($id)) {
                    $showButton = false;
                }
            } elseif ($originalAction == 'post') {
                $btnClass = 'btn-primary';
                $finalLabel = 'Post';
                if (!isset($id)) {
                    $showButton = false;
                }
            } elseif ($originalAction == 'cancel') {
                $btnClass = 'btn-danger';
                $finalLabel = 'Cancel';
                if (!isset($id)) {
                    $showButton = false;
                }
            }

            $isAllowed = $userHasAccess;

            if (!$isAllowed && in_array(strtolower($originalAction), ['save','create','edit'], true)) {
                $showButton = false;
            }
        @endphp

        @if($showButton && $finalLabel)
            @if($isAllowed)
                <button
                    type="submit"
                    value="{{$actionId}}"
                    class="btn btn-sm {{$btnClass}} staging-action-btn"
                    title="{{$finalLabel}}"
                    data-staging-action-id="{{$actionId}}"
                    data-staging-action-code="{{ strtolower($originalAction ?: $actionName) }}">
                    {{$finalLabel}}
                </button>
            @else
                <button
                    type="button"
                    class="btn btn-sm {{$btnClass}} stg-action-disabled"
                    title="{{$finalLabel}}"
                    data-stg-not-allowed="{{ __('message.staging_no_access') }}"
                    onclick="(function(el){var msg=el.getAttribute('data-stg-not-allowed')||'Not allowed';if(window.toastr&&typeof window.toastr.error==='function'){window.toastr.error(msg);}else{alert(msg);}})(this); return false;">
                    {{$finalLabel}}
                </button>
            @endif
        @endif
    @endforeach
@endif
    </div>
    <div class="stg-breadcrumb-right" style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
        @if($isCanceled)
            <span class="btn btn-sm btn-danger" style="cursor: default; pointer-events: none;">
                <i class="la la-ban"></i> Canceled
            </span>
        @elseif($isPosted && $isCompleted)
            <span class="btn btn-sm btn-success" style="cursor: default; pointer-events: none;">
                <i class="la la-check-circle"></i> Posted
            </span>
        @elseif(!$isCanceled && !$isPosted && !$isCompleted)
            <span class="btn btn-sm btn-success" style="cursor: default; pointer-events: none;">
                <i class="la la-spinner"></i> In Progress
            </span>
        @endif
        @include('staging_activity.breadcrumb')

    </div>
</div>

