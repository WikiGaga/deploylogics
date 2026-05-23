

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
    $unpostUserAccess = (bool) ($staging_data['unpost_user_access'] ?? false);



    $isPosted = false;

    $isCanceled = false;

    $currentDoc = $data['current'] ?? ($current ?? null);

    if ($currentDoc && isset($currentDoc->posted)) {

        if ((int) $currentDoc->posted === 1) {

            $isPosted = true;

        }

        if ((int) $currentDoc->posted === 2) {

            $isCanceled = true;

        }

    } elseif (!empty($data['page_data']['is_posted'])) {

        $isPosted = true;

    } elseif (!empty($data['page_data']['is_canceled'])) {

        $isCanceled = true;

    }



    $workflowActions = [];

    $unpostActions = [];

    foreach ($actions as $action) {

        $orig = strtolower((string) ($action->original_action ?? $action->stg_actions_name ?? ''));

        if (in_array($orig, ['un_post', 'unpost'], true)) {

            $unpostActions[] = $action;

        } else {

            $workflowActions[] = $action;

        }

    }



    $showWorkflowActions = !$isViewOnly && !$isCanceled && !$isPosted && !empty($workflowActions);

    $showUnpostActions = !$isViewOnly && ($isPosted || $isCanceled) && !empty($unpostActions) && $unpostUserAccess;

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

@if($showWorkflowActions)

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



    @foreach($workflowActions as $action)

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



@if($showUnpostActions)

    @foreach($unpostActions as $action)

        @php

            $actionId = $action->stg_actions_id;

            $originalAction = $action->original_action ?? $action->stg_actions_name ?? 'un_post';

        @endphp

        @if($unpostUserAccess)
            <button
                type="submit"
                value="{{$actionId}}"
                class="btn btn-sm btn-warning staging-action-btn"
                title="{{ __('message.unpost') }}"
                data-staging-action-id="{{$actionId}}"
                data-staging-action-code="un_post">
                {{ __('message.unpost') }}
            </button>
        @endif

    @endforeach

@endif

    </div>

    <div class="stg-breadcrumb-right" style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">

        @if($isCanceled)

            <span class="btn btn-sm btn-danger" style="cursor: default; pointer-events: none;">

                <i class="la la-ban"></i> Canceled

            </span>

        @elseif($isPosted)

            <span class="btn btn-sm btn-success" style="cursor: default; pointer-events: none;">

                <i class="la la-check-circle"></i> Posted

            </span>

        @elseif(!$isCanceled && !$isPosted)

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

