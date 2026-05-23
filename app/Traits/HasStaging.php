<?php

namespace App\Traits;

use App\Services\StagingService;
use App\Models\TblStgFormLog;
use App\Models\TblNotificationSetting;
use App\Models\TblSoftMenuDtl;
use App\Library\Utilities;
use App\Notifications\GlobalNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

trait HasStaging
{
    protected $stagingService;

    protected $stagingFormLogHasCasesColumnCache = null;

    protected function stagingFormLogHasCasesColumn(): bool
    {
        if ($this->stagingFormLogHasCasesColumnCache === null) {
            try {
                $this->stagingFormLogHasCasesColumnCache = Schema::hasColumn('tbl_stg_form_log', 'stg_form_cases_id');
            } catch (\Throwable $e) {
                $this->stagingFormLogHasCasesColumnCache = false;
            }
        }
        return $this->stagingFormLogHasCasesColumnCache;
    }

    protected function getStagingService()
    {
        if (!$this->stagingService) {
            $this->stagingService = new StagingService();
        }
        return $this->stagingService;
    }

    protected function getStagingData($formName, $currentFlowId = null, $formId = null)
    {
        $service = $this->getStagingService();

        $data = [
            'has_staging' => $service->hasStaging($formName, $formId),
            'flows' => $service->getFormFlows($formName, $currentFlowId, $formId),
            'actions' => [],
            'user_access' => false,
            'eligible_users' => collect([])
        ];

        if ($data['flows']['current']) {
            $currentFlowId = $data['flows']['current']->stg_flows_id;
            $data['actions'] = $service->getFormActions($formName, $currentFlowId, $formId);
            $data['user_access'] = $service->getUserAccess($formName, $currentFlowId, $formId);
            $data['eligible_users'] = $service->getEligibleUsers($formName, $currentFlowId, $formId);
        }

        return $data;
    }

    protected function getFormActivity($menuDtlId, $formId)
    {
        return TblStgFormLog::with('action_btn_dtl', 'flow_dtl', 'user', 'criteria_action')
            ->where('menu_dtl_id', $menuDtlId)
            ->where('document_id', $formId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    protected function logStagingActivity($menuDtlId, $formId, $flowId, $actionId, $remarks = null, $posted = 0, array $display = [])
    {
        $service = $this->getStagingService();
        $criteriaId = $service->getFlowCriteriaId($menuDtlId, $formId, true);

        $logData = [
            'stg_form_log_id' => Utilities::uuid(),
            'menu_dtl_id' => $menuDtlId,
            'document_id' => $formId,
            'user_id' => auth()->user()->id,
            'stg_flows_id' => $flowId,
            'stg_actions_id' => $actionId ?: '00000000-0000-0000-0000-000000000001',
            'remarks' => $this->encodeStagingLogRemarks($remarks, $display),
            'posted' => $posted,
            'stg_form_log_entry_status' => 1,
            'business_id' => auth()->user()->business_id,
            'company_id' => auth()->user()->company_id,
            'branch_id' => auth()->user()->branch_id,
        ];
        if ($this->stagingFormLogHasCasesColumn()) {
            $logData['stg_form_cases_id'] = $criteriaId ?? Utilities::uuid();
        }
        $log = TblStgFormLog::create($logData);

        return $log;
    }

    protected function encodeStagingLogRemarks($userRemarks, array $display): ?string
    {
        $userRemarks = $userRemarks !== null ? trim((string) $userRemarks) : '';
        if (empty($display['flow_name']) && empty($display['action_label']) && empty($display['action_code'])) {
            return $userRemarks !== '' ? $userRemarks : null;
        }
        $metaLine = '[STG_LOG_META]' . json_encode([
            'flow' => $display['flow_name'] ?? '',
            'action' => $display['action_label'] ?? '',
            'code' => strtolower((string) ($display['action_code'] ?? '')),
        ]);
        return $userRemarks !== '' ? $userRemarks . "\n" . $metaLine : $metaLine;
    }

    protected function normalizeStagingActionCode(string $code): string
    {
        $code = strtolower(str_replace([' ', '-'], '_', trim($code)));
        $aliases = [
            'send_back' => 'back',
            'sendback' => 'back',
            'send_forward' => 'forward',
            'unpost' => 'un_post',
        ];

        return $aliases[$code] ?? $code;
    }

    protected function stagingActionCodeMatches(string $code, string $expected): bool
    {
        return $this->normalizeStagingActionCode($code) === $expected;
    }

    protected function stagingActionIsWorkflowOnly(string $actionCode): bool
    {
        $code = $this->normalizeStagingActionCode($actionCode);
        return in_array($code, ['forward', 'back', 'post', 'cancel', 'un_post'], true);
    }

    protected function stagingActionIsSaveLike(string $actionCode): bool
    {
        $code = $this->normalizeStagingActionCode($actionCode);
        return in_array($code, ['save', 'create', 'edit'], true);
    }

    protected function resolveTransitionFlowId($request, array $flowsData, string $direction): ?string
    {
        if ($direction === 'back') {
            $fromRequest = trim((string) $request->input('prev_flow_id', ''));
            if ($fromRequest !== '') {
                return $fromRequest;
            }
            $prev = $flowsData['prev'] ?? null;
            if ($prev) {
                return (string) (is_object($prev) ? $prev->stg_flows_id : $prev);
            }
            return null;
        }

        $fromRequest = trim((string) $request->input('next_flow_id', ''));
        if ($fromRequest !== '') {
            return $fromRequest;
        }
        $next = $flowsData['next'] ?? null;
        if ($next) {
            return (string) (is_object($next) ? $next->stg_flows_id : $next);
        }

        return null;
    }

    protected function applyStagingFlowTransition($model, $targetFlowId): void
    {
        if (!$model || $targetFlowId === null || $targetFlowId === '') {
            return;
        }
        if (property_exists($model, 'current_stg_id') ||
            $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'current_stg_id')) {
            $model->current_stg_id = $targetFlowId;
        }
    }

    protected function formatStagingActionLabel(string $actionCode, bool $isNewDocument = false): string
    {
        $code = strtolower(trim($actionCode));
        if (in_array($code, ['save', 'create'], true)) {
            return $isNewDocument ? 'Create' : 'Update';
        }
        if ($code === 'edit') {
            return 'Update';
        }
        $labels = [
            'forward' => 'Forward',
            'post' => 'Post',
            'back' => 'Back',
            'cancel' => 'Cancel',
            'un_post' => 'Unpost',
            'unpost' => 'Unpost',
            'update' => 'Update',
        ];
        return $labels[$code] ?? ucfirst(str_replace('_', ' ', $code));
    }

    protected function resolveStagingFlowDisplayName($flowsData, $flowId): string
    {
        if ($flowsData && isset($flowsData['current']) && $flowsData['current']) {
            $current = $flowsData['current'];
            if (is_object($current)) {
                if (!empty($current->flow_name)) {
                    return $current->flow_name;
                }
                if (!empty($current->stg_flows_name)) {
                    return $current->stg_flows_name;
                }
            }
        }
        if ($flowsData && !empty($flowsData['all'])) {
            foreach ($flowsData['all'] as $flow) {
                if (is_object($flow) && (string) ($flow->stg_flows_id ?? '') === (string) $flowId) {
                    if (!empty($flow->flow_name)) {
                        return $flow->flow_name;
                    }
                    if (!empty($flow->stg_flows_name)) {
                        return $flow->stg_flows_name;
                    }
                }
            }
        }
        return $flowId ? 'Stage ' . $flowId : 'Unknown';
    }

    protected function resolveSaveLikeStagingAction(array $actions)
    {
        foreach ($actions as $action) {
            $name = $this->normalizeStagingActionCode((string) ($action->stg_actions_name ?? ''));
            $orig = $this->normalizeStagingActionCode((string) ($action->original_action ?? $name));
            if ($this->stagingActionIsSaveLike($name) || $this->stagingActionIsSaveLike($orig)) {
                return $action;
            }
        }

        return null;
    }

    protected function logStagingFormSaveActivity($request, $menuDtlId, $formId, $model, $isNew, $wasInStaging): void
    {
        $service = $this->getStagingService();
        $skipCriteriaConditions = $wasInStaging || $isNew || $service->isDocumentStagingEnrolled($model, $menuDtlId);
        $formIdForCriteria = $isNew ? null : $formId;

        $currentFlowId = $request->input('current_flow_id');
        if ($currentFlowId !== null) {
            $currentFlowId = trim((string) $currentFlowId);
        }
        if (empty($currentFlowId) && $model && !empty($model->current_stg_id)) {
            $currentFlowId = $model->current_stg_id;
        }
        if (empty($currentFlowId)) {
            $flows = $service->getFormFlows($menuDtlId, null, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model);
            $currentFlowId = !empty($flows['all']) ? $flows['all'][0]->stg_flows_id : null;
        }
        if (empty($currentFlowId)) {
            return;
        }

        $flowsData = $service->getFormFlows($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model);
        $actions = $service->getFormActions($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model);
        $resolvedAction = $this->resolveStagingFlowAction($request, $actions);

        if ($resolvedAction) {
            $resolvedCode = $this->normalizeStagingActionCode($this->stagingActionCodeFromResolved(
                $resolvedAction,
                $request,
                $menuDtlId,
                $currentFlowId,
                $formIdForCriteria ?: $formId,
                $skipCriteriaConditions
            ));
            if ($this->stagingActionIsWorkflowOnly($resolvedCode)
                && $this->stagingWorkflowActionShouldPersistFormFirst($resolvedCode)
                && $this->stagingUserHasSaveLikeAction($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model)) {
                $resolvedAction = $this->resolveSaveLikeStagingAction($actions);
            }
        }

        if (!$resolvedAction) {
            $resolvedAction = $this->resolveSaveLikeStagingAction($actions);
        }

        $actionId = $resolvedAction ? $resolvedAction->stg_actions_id : null;

        if (!$actionId) {
            return;
        }

        $actionCode = $this->stagingActionCodeFromResolved(
            $resolvedAction,
            $request,
            $menuDtlId,
            $currentFlowId,
            $formIdForCriteria ?: $formId,
            $skipCriteriaConditions
        );
        if (!in_array(strtolower($actionCode), ['save', 'create', 'edit'], true)) {
            $actionCode = $isNew ? 'create' : 'update';
        }

        $this->logStagingActivity(
            $menuDtlId,
            $formId,
            $currentFlowId,
            $actionId,
            $request->flow_remarks ?? null,
            0,
            [
                'flow_name' => $this->resolveStagingFlowDisplayName($flowsData, $currentFlowId),
                'action_label' => $this->formatStagingActionLabel($actionCode, $isNew),
                'action_code' => $isNew ? 'create' : 'update',
            ]
        );
    }

    protected function assertDocumentStateAllowsFormSave($request, $model, bool $isNew = false): void
    {
        if ($isNew || !$model) {
            return;
        }

        $model = $this->refreshModelRow($model);
        $freshPosted = $this->getDocumentPostedState($model);

        if ($request->has('document_posted_state') && $request->input('document_posted_state') !== null && $request->input('document_posted_state') !== '') {
            if ((int) $request->input('document_posted_state') !== $freshPosted) {
                throw new RuntimeException(__('message.document_state_changed_refresh'), 422);
            }
        }

        if ($freshPosted !== 0) {
            throw new RuntimeException(__('message.document_state_changed_refresh'), 422);
        }
    }

    protected function assertCanSaveWithStaging($request, $menuDtlId, $formId, $isNew = false, $model = null)
    {
        $service = $this->getStagingService();
        if ($model) {
            $model = $this->refreshModelRow($model);
        }

        $requestedActionCode = $this->normalizeStagingActionCode((string) $request->input('staging_action_code', ''));
        $isWorkflowOnlyRequest = $this->stagingActionIsWorkflowOnly($requestedActionCode);

        if (!$isNew && $model && !$isWorkflowOnlyRequest) {
            $this->assertDocumentStateAllowsFormSave($request, $model, $isNew);
        }

        $formIdForCriteria = $isNew ? null : $formId;
        $isAlreadyInStaging = !$isNew && $model && !empty($model->current_stg_id) && (int) ($model->posted ?? 0) === 0;
        $skipCriteriaConditions = $isAlreadyInStaging || $service->isDocumentStagingEnrolled($model, $menuDtlId);
        if (!$service->shouldUseStagingForDocument($menuDtlId, $formIdForCriteria ?: $formId, $model, $isAlreadyInStaging, $isNew)) {
            return;
        }

        $currentFlowId = $request->current_flow_id ?? null;
        if ($currentFlowId !== null) {
            $currentFlowId = trim((string) $currentFlowId);
        }
        if (empty($currentFlowId)) {
            $flows = $service->getFormFlows($menuDtlId, null, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model);
            $currentFlowId = !empty($flows['all']) ? $flows['all'][0]->stg_flows_id : null;
        }
        if (!$currentFlowId) {
            throw new RuntimeException(__('message.staging_flow_required'), 403);
        }

        if ($requestedActionCode === 'un_post') {
            if (!$service->userHasUnpostAccessForDocument($menuDtlId, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model)) {
                throw new RuntimeException(__('message.staging_no_access'), 403);
            }
        } elseif (!$service->getUserAccess($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model)) {
            throw new RuntimeException(__('message.staging_no_access'), 403);
        }

        $actions = $service->getFormActions($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model);
        $postedState = $model ? (int) ($model->posted ?? 0) : 0;
        if ($requestedActionCode === 'un_post' || in_array($postedState, [1, 2], true)) {
            $existingActionIds = [];
            foreach ($actions as $action) {
                $existingActionIds[(string) ($action->stg_actions_id ?? '')] = true;
            }
            foreach ($service->collectAccessibleUnpostActionsForDocument($menuDtlId, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model) as $unpostAction) {
                $actionId = (string) ($unpostAction->stg_actions_id ?? '');
                if ($actionId === '' || !isset($existingActionIds[$actionId])) {
                    $actions[] = $unpostAction;
                    if ($actionId !== '') {
                        $existingActionIds[$actionId] = true;
                    }
                }
            }
        }

        $resolvedAction = $this->resolveStagingFlowAction($request, $actions);
        $actionCode = $this->stagingActionCodeFromResolved($resolvedAction, $request, $menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $skipCriteriaConditions);

        $hasExplicitStagingAction = trim((string) $request->input('staging_action_code', '')) !== ''
            || trim((string) $request->input('current_actions_id', '')) !== '';

        if ($hasExplicitStagingAction && !$resolvedAction) {
            throw new RuntimeException(__('message.staging_action_not_allowed'), 403);
        }

        if (!$resolvedAction) {
            throw new RuntimeException(__('message.staging_save_not_allowed'), 403);
        }

        $this->assertStagingActionMatchesDocumentState($model, $actionCode, $request);
    }

    protected function handleStaging($request, $menuDtlId, $formId, $model, $isNew = false, ?array $notificationConfig = null)
    {
        $service = $this->getStagingService();
        $shouldRefresh = $model && method_exists($model, 'refresh') && !$isNew;
        if ($shouldRefresh && $this->modelHasStagingColumn($model, 'staging_apply') && $model->isDirty('staging_apply')) {
            $shouldRefresh = false;
        }
        if ($shouldRefresh) {
            $model->refresh();
        }
        $currentFlowId = $request->current_flow_id ?? null;
        if ($currentFlowId !== null) {
            $currentFlowId = trim((string) $currentFlowId);
        }
        if (!$isNew && $model && !empty($model->current_stg_id)) {
            $currentFlowId = trim((string) $model->current_stg_id);
        } elseif (!$isNew && trim((string) $request->input('document_current_stg_id', '')) !== '') {
            $currentFlowId = trim((string) $request->input('document_current_stg_id'));
        }

        $formIdForCriteria = $isNew ? null : $formId;
        $isAlreadyInStaging = !$isNew && $model && !empty($model->current_stg_id) && (int) ($model->posted ?? 0) === 0;
        $stagingEnrolled = $service->isDocumentStagingEnrolled($model, $menuDtlId);
        if (!$stagingEnrolled && !$isNew) {
            $docStgId = trim((string) $request->input('document_current_stg_id', ''));
            $reqFlowId = trim((string) ($request->input('current_flow_id') ?? ''));
            if ($docStgId !== '' || $reqFlowId !== '') {
                $stagingEnrolled = $service->hasStagingOrRemainsInStaging(
                    $menuDtlId,
                    $formIdForCriteria ?: $formId,
                    true,
                    $model
                );
            }
        }
        if (!$isNew && !$isAlreadyInStaging && $stagingEnrolled && trim((string) $request->input('document_current_stg_id', '')) !== '') {
            $isAlreadyInStaging = (int) ($model->posted ?? 0) === 0;
        }
        $skipCriteriaConditions = $isAlreadyInStaging || $stagingEnrolled;
        if (!$stagingEnrolled && !$service->shouldUseStagingForDocument($menuDtlId, $formIdForCriteria ?: $formId, $model, $isAlreadyInStaging, $isNew)) {
            return;
        }
        if (empty($currentFlowId)) {
            $flows = $service->getFormFlows($menuDtlId, null, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model);
            $currentFlowId = !empty($flows['all']) ? $flows['all'][0]->stg_flows_id : null;
        }

        $requestedActionCode = $this->normalizeStagingActionCode((string) $request->input('staging_action_code', ''));
        $postedState = $model ? (int) ($model->posted ?? 0) : 0;

        if ($requestedActionCode === 'un_post') {
            if (!$service->userHasUnpostAccessForDocument($menuDtlId, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model)) {
                throw new RuntimeException(__('message.staging_no_access'), 403);
            }
        } elseif ($currentFlowId && !$service->getUserAccess($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model)) {
            throw new RuntimeException(__('message.staging_no_access'), 403);
        }

        $actions = [];
        if ($currentFlowId) {
            $actions = $service->getFormActions($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model);
        }
        if ($requestedActionCode === 'un_post' || in_array($postedState, [1, 2], true)) {
            $existingActionIds = [];
            foreach ($actions as $action) {
                $existingActionIds[(string) ($action->stg_actions_id ?? '')] = true;
            }
            foreach ($service->collectAccessibleUnpostActionsForDocument($menuDtlId, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model) as $unpostAction) {
                $actionId = (string) ($unpostAction->stg_actions_id ?? '');
                if ($actionId === '' || !isset($existingActionIds[$actionId])) {
                    $actions[] = $unpostAction;
                    if ($actionId !== '') {
                        $existingActionIds[$actionId] = true;
                    }
                }
            }
        }

        $resolvedAction = $this->resolveStagingFlowAction($request, $actions);
        $actionId = $resolvedAction ? $resolvedAction->stg_actions_id : null;
        $actionCode = $this->stagingActionCodeFromResolved(
            $resolvedAction,
            $request,
            $menuDtlId,
            $currentFlowId,
            $formIdForCriteria ?: $formId,
            $skipCriteriaConditions
        );

        $hasExplicitStagingAction = trim((string) $request->input('staging_action_code', '')) !== ''
            || trim((string) $request->input('current_actions_id', '')) !== '';
        if ($hasExplicitStagingAction && !$resolvedAction) {
            throw new RuntimeException(__('message.staging_action_not_allowed'), 403);
        }

        if ($resolvedAction) {
            $this->assertStagingActionMatchesDocumentState($model, $actionCode, $request);
        }

        if ($isNew && $currentFlowId && $model && (property_exists($model, 'current_stg_id') || $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'current_stg_id'))) {
            $model->current_stg_id = $currentFlowId;
            if (property_exists($model, 'staging_apply') || $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'staging_apply')) {
                $model->staging_apply = 1;
            }
        }

        $actionName = null;
        $originalAction = null;
        if ($actionId) {
            foreach ($actions as $action) {
                if ((string) $action->stg_actions_id === (string) $actionId) {
                    $actionName = $action->stg_actions_name;
                    $originalAction = $action->original_action ?? $actionName;
                    break;
                }
            }
        }

        $flowsData = $service->getFormFlows($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $skipCriteriaConditions, $model);

        $normalizedActionCode = $this->normalizeStagingActionCode($actionCode);
        $isSaveLikeAction = $this->stagingActionIsSaveLike($normalizedActionCode);
        if ($currentFlowId && $actionId && !$isSaveLikeAction) {
            $this->logStagingActivity(
                $menuDtlId,
                $formId,
                $currentFlowId,
                $actionId,
                $request->flow_remarks ?? null,
                0,
                [
                    'flow_name' => $this->resolveStagingFlowDisplayName($flowsData, $currentFlowId),
                    'action_label' => $this->formatStagingActionLabel($originalAction ?: $actionName ?: $actionCode, $isNew),
                    'action_code' => strtolower((string) ($originalAction ?: $actionName ?: $actionCode)),
                ]
            );
        }

        if ($currentFlowId && $actionId) {
            $codesToCheck = array_filter([
                $normalizedActionCode,
                $this->normalizeStagingActionCode((string) $originalAction),
                $this->normalizeStagingActionCode((string) $actionName),
            ]);

            $isUnpostLike = in_array('un_post', $codesToCheck, true);
            $isCancelLike = in_array('cancel', $codesToCheck, true);
            $isPostLike = in_array('post', $codesToCheck, true);
            $isForwardLike = in_array('forward', $codesToCheck, true);
            $isBackLike = in_array('back', $codesToCheck, true);

            $prevFlowId = $this->resolveTransitionFlowId($request, $flowsData, 'back');
            $nextFlowId = $this->resolveTransitionFlowId($request, $flowsData, 'forward');

            if ($isUnpostLike) {
                if ($model && (property_exists($model, 'posted') ||
                    $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'posted'))) {
                    $model->posted = 0;
                }
            } elseif ($isCancelLike) {
                if ($model && (property_exists($model, 'staging_apply') ||
                    $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'staging_apply'))) {
                    $model->staging_apply = 1;
                }
                if ($model && (property_exists($model, 'posted') ||
                    $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'posted'))) {
                    $model->posted = 2;
                }
                \App\Models\TblStgFormLog::where('menu_dtl_id', $menuDtlId)
                    ->where('document_id', $formId)
                    ->update(['posted' => 1]);
            } elseif ($isPostLike) {
                if ($model && (property_exists($model, 'staging_apply') ||
                    $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'staging_apply'))) {
                    $model->staging_apply = 1;
                }
                if ($model && (property_exists($model, 'posted') ||
                    $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'posted'))) {
                    $model->posted = 1;
                }
                \App\Models\TblStgFormLog::where('menu_dtl_id', $menuDtlId)
                    ->where('document_id', $formId)
                    ->update(['posted' => 1]);
            } elseif ($isForwardLike && $nextFlowId !== null && $nextFlowId !== '') {
                $this->applyStagingFlowTransition($model, $nextFlowId);
            } elseif ($isBackLike && $prevFlowId !== null && $prevFlowId !== '') {
                $this->applyStagingFlowTransition($model, $prevFlowId);
            } elseif ($isForwardLike && ($nextFlowId === null || $nextFlowId === '')) {
                if ($model && (property_exists($model, 'staging_apply') ||
                    $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'staging_apply'))) {
                    $model->staging_apply = 1;
                }
                if ($model && (property_exists($model, 'posted') ||
                    $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'posted'))) {
                    $model->posted = 1;
                }
                \App\Models\TblStgFormLog::where('menu_dtl_id', $menuDtlId)
                    ->where('document_id', $formId)
                    ->update(['posted' => 1]);
            }

            if ($notificationConfig && !empty($notificationConfig['listing_view']) && isset($notificationConfig['form_path'])) {
                $matchedNotificationSetting = $this->getNotificationSettingForMenu($menuDtlId);
                if ($matchedNotificationSetting) {
                    $this->sendStagingNotification(
                        $service,
                        $menuDtlId,
                        $formId,
                        $formIdForCriteria ?: $formId,
                        $isAlreadyInStaging,
                        $currentFlowId,
                        $actionName,
                        $originalAction,
                        $flowsData,
                        $nextFlowId,
                        $prevFlowId,
                        $isPostLike,
                        $isForwardLike,
                        $isBackLike,
                        $isCancelLike,
                        $isUnpostLike,
                        $notificationConfig,
                        $model
                    );
                }
            }

            if ($model && $model->exists && $model->isDirty()) {
                $model->save();
            }
        } elseif ($isNew) {
            $flows = $service->getFormFlows($menuDtlId, null, null);
            if (!empty($flows['all']) && $model &&
                (property_exists($model, 'current_stg_id') ||
                 $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'current_stg_id'))) {
                $model->current_stg_id = $flows['all'][0]->stg_flows_id;
                if (property_exists($model, 'staging_apply') || $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'staging_apply')) {
                    $model->staging_apply = 1;
                }
            }
            if ($notificationConfig && !empty($notificationConfig['listing_view']) && isset($notificationConfig['form_path']) && !empty($flows['all'])) {
                $matchedNotificationSetting = $this->getNotificationSettingForMenu($menuDtlId);
                if ($matchedNotificationSetting) {
                    $firstFlowId = $flows['all'][0]->stg_flows_id;
                    $stageName = $flows['all'][0]->stg_flows_name ?? 'Draft';
                    $users = $service->getEligibleUsers($menuDtlId, $firstFlowId, $formId, false);
                    $url = rtrim($notificationConfig['form_path'], '/') . '/' . $formId;
                    $code = isset($notificationConfig['document_code_key']) && $model
                        ? ($model->{$notificationConfig['document_code_key']} ?? null)
                        : null;
                    if ($users->isNotEmpty()) {
                        try {
                            Notification::send($users, new GlobalNotification(
                                $notificationConfig['listing_view'],
                                url($url),
                                [
                                    'stage' => $stageName,
                                    'document-code' => $code,
                                ]
                            ));
                        } catch (\Throwable $e) {
                            Log::warning('Staging notification failed', ['error' => $e->getMessage()]);
                        }
                    }
                }
            }
        }
    }

    protected function resolveRequestedStagingActionCode($request, $menuDtlId, $currentFlowId, $formId, $isAlreadyInStaging = false, $model = null): string
    {
        $requestedCode = $this->normalizeStagingActionCode((string) $request->input('staging_action_code', ''));
        if ($requestedCode !== '') {
            return $requestedCode;
        }

        $requestedActionId = $request->input('current_actions_id');
        if ($requestedActionId !== null) {
            $requestedActionId = trim((string) $requestedActionId);
        }
        if ($requestedActionId === '') {
            return '';
        }

        $service = $this->getStagingService();
        $skipCriteriaConditions = $isAlreadyInStaging || $service->isDocumentStagingEnrolled($model, $menuDtlId);
        $actions = $service->getFormActions($menuDtlId, $currentFlowId, $formId, $skipCriteriaConditions, $model);
        foreach ($actions as $action) {
            if ((string) ($action->stg_actions_id ?? '') === (string) $requestedActionId) {
                $name = $this->normalizeStagingActionCode((string) ($action->stg_actions_name ?? ''));
                $orig = $this->normalizeStagingActionCode((string) ($action->original_action ?? $name));
                return $orig !== '' ? $orig : $name;
            }
        }

        return '';
    }

    protected function stagingUserHasSaveLikeAction($menuDtlId, $currentFlowId, $formId, $skipConditionCheck = false, $model = null): bool
    {
        $service = $this->getStagingService();
        $actions = $service->getFormActions($menuDtlId, $currentFlowId, $formId, $skipConditionCheck, $model);
        foreach ($actions as $action) {
            $name = strtolower((string) ($action->stg_actions_name ?? ''));
            $orig = strtolower((string) ($action->original_action ?? $name));
            if (in_array($name, ['save', 'create', 'edit'], true) || in_array($orig, ['save', 'create', 'edit'], true)) {
                return true;
            }
        }
        return false;
    }

    protected function modelHasStagingColumn($model, string $column): bool
    {
        return $model && (
            property_exists($model, $column) ||
            $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), $column)
        );
    }

    protected function finalizeDocumentStaging($request, $menuDtlId, $formId, $model, $isNew, array $options = []): void
    {
        if (!$model) {
            return;
        }

        $wasInStaging = !$isNew && !empty($model->current_stg_id) && (int) ($model->posted ?? 0) === 0;
        $stagingService = $this->getStagingService();
        $retainStaging = $stagingService->documentRetainsStagingWorkflow($model, $wasInStaging, $menuDtlId);
        $flowCriteriaActive = $stagingService->hasStagingOrRemainsInStaging($menuDtlId, $formId, $retainStaging, $model);
        $shouldUseStaging = $stagingService->shouldUseStagingForDocument($menuDtlId, $formId, $model, $wasInStaging, $isNew);

        $notificationConfig = $options['notification'] ?? [];
        $postedWhenExempt = array_key_exists('posted_when_exempt', $options) ? $options['posted_when_exempt'] : 0;
        $stgLogPostedWhenExempt = array_key_exists('stg_log_posted_when_exempt', $options)
            ? $options['stg_log_posted_when_exempt']
            : 0;

        if (!$flowCriteriaActive && !$retainStaging) {
            if ($this->modelHasStagingColumn($model, 'current_stg_id')) {
                $model->current_stg_id = null;
            }
            if ($this->modelHasStagingColumn($model, 'staging_apply')) {
                $model->staging_apply = 0;
            }
            if ($this->modelHasStagingColumn($model, 'posted')) {
                $model->posted = $postedWhenExempt;
            }
            $model->save();

            if ($stgLogPostedWhenExempt !== null) {
                \App\Models\TblStgFormLog::where('menu_dtl_id', $menuDtlId)
                    ->where('document_id', $formId)
                    ->update(['posted' => $stgLogPostedWhenExempt]);
            }

            if (!empty($options['sync_after_save']) && is_callable($options['sync_after_save'])) {
                $options['sync_after_save']($model);
            }

            return;
        }

        if (!$shouldUseStaging) {
            return;
        }

        if ($this->modelHasStagingColumn($model, 'posted') && (int) ($model->posted ?? 0) === 1) {
            $model->posted = 0;
        }

        if ($this->modelHasStagingColumn($model, 'staging_apply')) {
            $model->staging_apply = 1;
        }
        if ($this->modelHasStagingColumn($model, 'current_stg_id') && empty($model->current_stg_id)) {
            $skipForEnrolled = $wasInStaging || $stagingService->isDocumentStagingEnrolled($model, $menuDtlId);
            $flows = $stagingService->getFormFlows($menuDtlId, null, $formId, $skipForEnrolled, $model);
            if (!empty($flows['all'])) {
                $model->current_stg_id = $flows['all'][0]->stg_flows_id;
            }
        }

        if (!empty($options['preserved_staging']) && (int) ($options['preserved_staging']['staging_apply'] ?? 0) === 1) {
            if ($this->modelHasStagingColumn($model, 'staging_apply')) {
                $model->staging_apply = 1;
            }
            if ($this->modelHasStagingColumn($model, 'current_stg_id') && !empty($options['preserved_staging']['current_stg_id'])) {
                $model->current_stg_id = $options['preserved_staging']['current_stg_id'];
            }
            if ($this->modelHasStagingColumn($model, 'posted') && array_key_exists('posted', $options['preserved_staging'])) {
                $model->posted = (int) $options['preserved_staging']['posted'];
            }
        }

        if (!empty($notificationConfig)) {
            $this->handleStaging($request, $menuDtlId, $formId, $model, $isNew, $notificationConfig);
        }

        $this->logStagingFormSaveActivity($request, $menuDtlId, $formId, $model, $isNew, $wasInStaging);

        $actionCode = $this->resolveRequestedStagingActionCode($request, $menuDtlId, $request->input('current_flow_id'), $formId, $wasInStaging, $model);
        if ($this->stagingActionIsWorkflowOnly($actionCode)) {
            $skipForEnrolled = $wasInStaging || $stagingService->isDocumentStagingEnrolled($model, $menuDtlId);
            $flowsData = $stagingService->getFormFlows(
                $menuDtlId,
                $request->input('current_flow_id'),
                $formId,
                $skipForEnrolled,
                $model
            );
            if ($this->stagingActionCodeMatches($actionCode, 'forward')) {
                $this->applyStagingFlowTransition($model, $this->resolveTransitionFlowId($request, $flowsData, 'forward'));
            } elseif ($this->stagingActionCodeMatches($actionCode, 'back')) {
                $this->applyStagingFlowTransition($model, $this->resolveTransitionFlowId($request, $flowsData, 'back'));
            }
        }

        $model->save();

        if (!empty($options['sync_after_save']) && is_callable($options['sync_after_save'])) {
            $options['sync_after_save']($model);
        }
    }

    protected function stagingShouldPersistFormChanges($request, $menuDtlId, $formId, $model = null): bool
    {
        $currentFlowId = $request->input('current_flow_id');
        if ($currentFlowId !== null) {
            $currentFlowId = trim((string) $currentFlowId);
        }

        if (empty($currentFlowId) || empty($formId)) {
            return true;
        }

        $isAlreadyInStaging = $model && !empty($model->current_stg_id) && (int)($model->posted ?? 0) === 0;
        $service = $this->getStagingService();
        $stagingEnrolled = $service->isDocumentStagingEnrolled($model, $menuDtlId);

        if (!$stagingEnrolled && !$service->shouldUseStagingForDocument($menuDtlId, $formId, $model, $isAlreadyInStaging, false)) {
            return true;
        }

        $skipCriteriaConditions = $isAlreadyInStaging || $stagingEnrolled;
        $actionCode = $this->resolveRequestedStagingActionCode($request, $menuDtlId, $currentFlowId, $formId, $isAlreadyInStaging, $model);
        if ($actionCode === '') {
            return true;
        }

        $actionCode = $this->normalizeStagingActionCode($actionCode);

        if ($this->stagingActionIsSaveLike($actionCode)) {
            return true;
        }

        if (!$this->stagingActionIsWorkflowOnly($actionCode)) {
            return true;
        }

        if (!$this->stagingWorkflowActionShouldPersistFormFirst($actionCode)) {
            return false;
        }

        return $this->stagingUserHasSaveLikeAction($menuDtlId, $currentFlowId, $formId, $skipCriteriaConditions, $model);
    }

    protected function stagingWorkflowActionShouldPersistFormFirst(string $actionCode): bool
    {
        return in_array($this->normalizeStagingActionCode($actionCode), ['forward', 'post', 'back'], true);
    }

    protected function sendStagingNotification(
        $service,
        $menuDtlId,
        $formId,
        $formIdForCriteria,
        $isAlreadyInStaging,
        $currentFlowId,
        $actionName,
        $originalAction,
        $flowsData,
        $nextFlowId,
        $prevFlowId,
        $isPostLike,
        $isForwardLike,
        $isBackLike,
        $isCancelLike,
        $isUnpostLike,
        array $notificationConfig,
        $model = null
    ) {
        $stage = 'Draft';
        $targetFlowId = $currentFlowId;

        if ($isUnpostLike) {
            $stage = is_object($flowsData['current'] ?? null)
                ? ($flowsData['current']->stg_flows_name ?? 'Draft')
                : 'Draft';
        } elseif ($isCancelLike) {
            $stage = 'Cancelled';
        } elseif ($isPostLike || ($isForwardLike && ($nextFlowId === null || $nextFlowId === ''))) {
            $stage = 'Published';
        } elseif ($isForwardLike && $nextFlowId) {
            $stage = is_object($flowsData['next'] ?? null) ? ($flowsData['next']->stg_flows_name ?? '') : '';
            $targetFlowId = $nextFlowId;
        } elseif ($isBackLike && $prevFlowId) {
            $stage = is_object($flowsData['prev'] ?? null) ? ($flowsData['prev']->stg_flows_name ?? '') : '';
            $targetFlowId = $prevFlowId;
        } else {
            $stage = is_object($flowsData['current'] ?? null) ? ($flowsData['current']->stg_flows_name ?? '') : '';
        }

        if (empty($stage)) {
            $stage = 'Draft';
        }

        $users = $service->getEligibleUsers($menuDtlId, $targetFlowId, $formIdForCriteria, $isAlreadyInStaging);
        if ($users->isEmpty()) {
            return;
        }

        $url = rtrim($notificationConfig['form_path'], '/') . '/' . $formId;
        $code = isset($notificationConfig['document_code_key']) && $model
            ? ($model->{$notificationConfig['document_code_key']} ?? null)
            : null;

        try {
            Notification::send($users, new GlobalNotification(
                $notificationConfig['listing_view'],
                url($url),
                [
                    'stage' => $stage,
                    'document-code' => $code,
                ]
            ));
        } catch (\Throwable $e) {
            Log::warning('Staging notification failed', ['error' => $e->getMessage()]);
        }
    }

    protected function getNotificationSettingForMenu($menuDtlId)
    {
        $menu = TblSoftMenuDtl::find($menuDtlId);
        if (!$menu || empty($menu->menu_dtl_table_name)) {
            return null;
        }

        return TblNotificationSetting::where('key', $menu->menu_dtl_table_name)
            ->orderByDesc('created_at')
            ->first();
    }

    protected function documentFormStayRedirect(string $basePath, $formId): string
    {
        return rtrim($basePath, '/') . '/form/' . $formId;
    }

    protected function refreshModelRow($model)
    {
        if ($model && method_exists($model, 'fresh')) {
            $fresh = $model->fresh();
            return $fresh ?: $model;
        }

        return $model;
    }

    protected function getDocumentPostedState($model): int
    {
        if (!$model || !isset($model->posted)) {
            return 0;
        }

        return (int) $model->posted;
    }

    protected function clearFormUpdateActionIfDocumentNotEditable(array &$pageData, $model): void
    {
        if ($this->getDocumentPostedState($model) !== 0) {
            $pageData['action'] = '';
        }
    }

    protected function assertDocumentStateAllowsUmAction($model, string $action): void
    {
        $posted = $this->getDocumentPostedState($model);
        $action = strtolower($action);

        if ($action === 'post') {
            if ($posted === 1) {
                throw new RuntimeException(__('message.document_already_posted_refresh'), 422);
            }
            if ($posted === 2) {
                throw new RuntimeException(__('message.document_state_changed_refresh'), 422);
            }
            return;
        }

        if ($action === 'unpost') {
            if (!in_array($posted, [1, 2], true)) {
                throw new RuntimeException(__('message.document_not_posted_refresh'), 422);
            }
            return;
        }

        if ($action === 'cancel') {
            if ($posted === 1) {
                throw new RuntimeException(__('message.document_already_posted_refresh'), 422);
            }
            if ($posted === 2) {
                throw new RuntimeException(__('message.document_already_canceled'), 422);
            }
        }
    }

    protected function assertStagingActionMatchesDocumentState($model, string $actionCode, $request = null): void
    {
        if (!$model || !isset($model->posted)) {
            return;
        }

        if ($request && $request->has('document_posted_state') && $request->input('document_posted_state') !== null && $request->input('document_posted_state') !== '') {
            $freshPosted = $this->getDocumentPostedState($model);
            if ((int) $request->input('document_posted_state') !== $freshPosted) {
                throw new RuntimeException(__('message.document_state_changed_refresh'), 422);
            }
        }

        if ($request && $request->has('document_current_stg_id') && trim((string) $request->input('document_current_stg_id')) !== '') {
            $freshStgId = isset($model->current_stg_id) ? trim((string) $model->current_stg_id) : '';
            if ($freshStgId !== '' && trim((string) $request->input('document_current_stg_id')) !== $freshStgId) {
                throw new RuntimeException(__('message.document_state_changed_refresh'), 422);
            }
        }

        $posted = $this->getDocumentPostedState($model);
        $code = strtolower($actionCode);

        if (in_array($code, ['un_post', 'unpost'], true)) {
            if (!in_array($posted, [1, 2], true)) {
                throw new RuntimeException(__('message.document_state_changed_refresh'), 422);
            }
            return;
        }

        if ($posted !== 0) {
            throw new RuntimeException(__('message.document_state_changed_refresh'), 422);
        }
    }

    protected function resolveStagingFlowAction($request, array $actions)
    {
        $requestedActionId = $request->current_actions_id ?? null;
        if ($requestedActionId !== null) {
            $requestedActionId = trim((string) $requestedActionId);
        }

        if ($requestedActionId !== '') {
            foreach ($actions as $action) {
                if ((string) $action->stg_actions_id === (string) $requestedActionId) {
                    return $action;
                }
            }
            return null;
        }

        $requestedCode = $this->normalizeStagingActionCode((string) $request->input('staging_action_code', ''));
        if ($requestedCode !== '') {
            foreach ($actions as $action) {
                $name = $this->normalizeStagingActionCode((string) ($action->stg_actions_name ?? ''));
                $orig = $this->normalizeStagingActionCode((string) ($action->original_action ?? $name));
                if ($name === $requestedCode || $orig === $requestedCode) {
                    return $action;
                }
            }
            return null;
        }

        foreach ($actions as $action) {
            $name = $this->normalizeStagingActionCode((string) ($action->stg_actions_name ?? ''));
            $orig = $this->normalizeStagingActionCode((string) ($action->original_action ?? $name));
            if ($this->stagingActionIsSaveLike($name) || $this->stagingActionIsSaveLike($orig)) {
                return $action;
            }
        }

        return null;
    }

    protected function stagingActionCodeFromResolved($resolvedAction, $request, $menuDtlId, $currentFlowId, $formId, $skipCriteriaConditions): string
    {
        if ($resolvedAction) {
            $orig = $this->normalizeStagingActionCode((string) ($resolvedAction->original_action ?? ''));
            $name = $this->normalizeStagingActionCode((string) ($resolvedAction->stg_actions_name ?? ''));
            return $orig !== '' ? $orig : $name;
        }

        return $this->resolveRequestedStagingActionCode($request, $menuDtlId, $currentFlowId, $formId, $skipCriteriaConditions);
    }

    protected function guardUmDocumentAction($menuDtlId, $model, string $permSuffix, string $stateAction): void
    {
        if (!auth()->user()->isAbleTo($menuDtlId . '-' . $permSuffix)) {
            throw new RuntimeException('You do not have permission to ' . $stateAction . '.', 403);
        }
        if (!$model) {
            throw new RuntimeException(__('message.document_not_found'), 422);
        }
        $model = $this->refreshModelRow($model);
        if ($this->isUmActionBlockedForStagingEnrolled($model, $menuDtlId)) {
            throw new RuntimeException(__('message.staging_um_action_not_allowed'), 422);
        }
        $this->assertDocumentStateAllowsUmAction($model, $stateAction);
    }

    protected function umJsonErrorFromException(RuntimeException $e)
    {
        $code = (int) $e->getCode();
        if ($code < 400 || $code >= 600) {
            $code = 422;
        }

        return response()->json(['status' => 'error', 'message' => $e->getMessage()], $code);
    }

    protected function isUmActionBlockedForStagingEnrolled($model, $menuDtlId): bool
    {
        return $model && $this->getStagingService()->isDocumentStagingEnrolled($model, $menuDtlId);
    }

    protected function umStagingEnrolledErrorResponse()
    {
        return response()->json([
            'status' => 'error',
            'message' => __('message.staging_um_action_not_allowed'),
        ], 422);
    }

    protected function umPermissionDeniedResponse(string $actionLabel = 'perform this action')
    {
        return response()->json([
            'status' => 'error',
            'message' => 'You do not have permission to ' . $actionLabel . '.',
        ], 403);
    }

    protected function jsonErrorStagingEnrolledForUm()
    {
        return $this->jsonErrorResponse([], __('message.staging_um_action_not_allowed'), 422);
    }
}
