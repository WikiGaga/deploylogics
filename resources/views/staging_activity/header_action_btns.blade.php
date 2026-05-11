
@php
    $isViewOnly = (string) request('view', '') === '1';
    $currentFlowId = isset($staging_data['flows']['current']) && is_object($staging_data['flows']['current'])
        ? $staging_data['flows']['current']->stg_flows_id
        : null;

    $flowsAll = $staging_data['flows']['all'] ?? [];
    $firstFlowId = null;
    if (!empty($flowsAll)) {
        $first = $flowsAll[0] ?? null;
        $firstFlowId = is_object($first) && isset($first->stg_flows_id) ? $first->stg_flows_id : (is_numeric($first) ? $first : null);
    }

    $currentFlowName = null;
    if (isset($staging_data['flows']['current']) && is_object($staging_data['flows']['current']) && isset($staging_data['flows']['current']->stg_flows_name)) {
        $currentFlowName = $staging_data['flows']['current']->stg_flows_name;
    }
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
<style>
    .stg-action-disabled {
        opacity: 0.55 !important;
        cursor: not-allowed !important;
        box-shadow: none !important;
        pointer-events: auto;
    }
    .stg-action-disabled:hover,
    .stg-action-disabled:focus {
        opacity: 0.55 !important;
        cursor: not-allowed !important;
    }
</style>

<div class="stg-header-row" style="display: flex; justify-content: space-between; align-items: center; flex: 1; min-width: 0; gap: 8px;">
    <div class="stg-actions-left">
@if(!$isViewOnly && !$isCanceled && !($isPosted && $isCompleted) && !empty($actions))
    @if(!$userHasAccess && isset($id))
        <button
            type="button"
            class="btn btn-sm btn-success stg-action-disabled"
            title="{{ __('message.staging_no_access') }}"
            aria-disabled="true"
            data-stg-not-allowed="{{ __('message.staging_no_access') }}">
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
                    title="{{ __('message.staging_no_access') }}"
                    aria-disabled="true"
                    data-stg-not-allowed="{{ __('message.staging_no_access') }}">
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
            @php
                $isDraft = !isset($id) || ($currentFlowId !== null && $firstFlowId !== null && (string)$currentFlowId === (string)$firstFlowId);
                $statusLabel = $isDraft ? 'Draft' : ($currentFlowName ?: 'In Review');
                $statusClass = $isDraft ? 'btn-info' : 'btn-warning';
                $statusIcon = $isDraft ? 'la la-file-alt' : 'la la-stream';
            @endphp
            <span class="btn btn-sm {{ $statusClass }}" style="cursor: default; pointer-events: none;">
                <i class="{{ $statusIcon }}"></i> {{ $statusLabel }}
            </span>
        @endif
        @include('staging_activity.breadcrumb')

    </div>
</div>

