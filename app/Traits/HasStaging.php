<?php

namespace App\Traits;

use App\Services\StagingService;
use App\Models\TblStgFormLog;
use App\Library\Utilities;
use App\Notifications\GlobalNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

trait HasStaging
{
    protected $stagingService;

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
        return TblStgFormLog::with('action_btn_dtl', 'flow_dtl', 'user', 'criteria_action', 'flow_criteria_flow')
            ->where('menu_dtl_id', $menuDtlId)
            ->where('document_id', $formId)
            ->where(Utilities::currentBCB())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    protected function logStagingActivity($menuDtlId, $formId, $flowId, $actionId, $remarks = null, $posted = 0)
    {
        $service = $this->getStagingService();
        $criteriaId = $service->getFlowCriteriaId($menuDtlId, $formId, true);

        $log = TblStgFormLog::create([
            'stg_form_log_id' => Utilities::uuid(),
            'menu_dtl_id' => $menuDtlId,
            'document_id' => $formId,
            'stg_form_cases_id' => $criteriaId ?? Utilities::uuid(),
            'user_id' => auth()->user()->id,
            'stg_flows_id' => $flowId,
            'stg_actions_id' => $actionId ?: '00000000-0000-0000-0000-000000000001',
            'remarks' => $remarks ? trim((string) $remarks) : null,
            'posted' => $posted,
            'stg_form_log_entry_status' => 1,
            'business_id' => auth()->user()->business_id,
            'company_id' => auth()->user()->company_id,
            'branch_id' => auth()->user()->branch_id,
        ]);

        return $log;
    }

    protected function assertCanSaveWithStaging($request, $menuDtlId, $formId, $isNew = false, $model = null)
    {
        $service = $this->getStagingService();
        $formIdForCriteria = $isNew ? null : $formId;
        $isAlreadyInStaging = !$isNew && $model && !empty($model->current_stg_id) && (int)($model->posted ?? 0) === 0;
        if (!$service->hasStagingOrRemainsInStaging($menuDtlId, $formIdForCriteria ?: $formId, $isAlreadyInStaging)) {
            return;
        }
        if ($model && isset($model->posted) && (int) $model->posted === 2) {
            throw new RuntimeException(__('message.document_canceled_no_action'), 403);
        }
        $currentFlowId = $request->current_flow_id ?? null;
        if ($currentFlowId !== null) {
            $currentFlowId = trim((string) $currentFlowId);
        }
        if (empty($currentFlowId)) {
            $flows = $service->getFormFlows($menuDtlId, null, $formIdForCriteria ?: $formId, $isAlreadyInStaging);
            $currentFlowId = !empty($flows['all']) ? $flows['all'][0]->stg_flows_id : null;
        }
        if (!$currentFlowId) {
            throw new RuntimeException(__('message.staging_flow_required'), 403);
        }
        if (!$service->getUserAccess($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $isAlreadyInStaging)) {
            throw new RuntimeException(__('message.staging_no_access'), 403);
        }
        $actions = $service->getFormActions($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $isAlreadyInStaging);
        $requestedActionId = $request->current_actions_id ?? null;
        if ($requestedActionId !== null) {
            $requestedActionId = trim((string) $requestedActionId);
        }

        $resolvedAction = null;
        if (!empty($requestedActionId)) {
            foreach ($actions as $action) {
                if ((string) $action->stg_actions_id === (string) $requestedActionId) {
                    $resolvedAction = $action;
                    break;
                }
            }
            if (!$resolvedAction) {
                throw new RuntimeException(__('message.staging_save_not_allowed'), 403);
            }
            return;
        }

        foreach ($actions as $action) {
            $name = strtolower($action->stg_actions_name ?? '');
            $orig = strtolower($action->original_action ?? $name);
            if (in_array($name, ['save', 'create', 'edit'], true) || in_array($orig, ['save', 'create', 'edit'], true)) {
                $resolvedAction = $action;
                break;
            }
        }

        if (!$resolvedAction) {
            throw new RuntimeException(__('message.staging_save_not_allowed'), 403);
        }
    }

    protected function handleStaging($request, $menuDtlId, $formId, $model, $isNew = false, ?array $notificationConfig = null)
    {
        $service = $this->getStagingService();
        $currentFlowId = $request->current_flow_id ?? null;
        if ($currentFlowId !== null) {
            $currentFlowId = trim((string) $currentFlowId);
        }

        $formIdForCriteria = $isNew ? null : $formId;
        $isAlreadyInStaging = !$isNew && $model && !empty($model->current_stg_id) && (int)($model->posted ?? 0) === 0;
        if (!$service->hasStagingOrRemainsInStaging($menuDtlId, $formIdForCriteria ?: $formId, $isAlreadyInStaging)) {
            return;
        }
        if (empty($currentFlowId)) {
            $flows = $service->getFormFlows($menuDtlId, null, $formIdForCriteria ?: $formId, $isAlreadyInStaging);
            $currentFlowId = !empty($flows['all']) ? $flows['all'][0]->stg_flows_id : null;
        }

        if ($currentFlowId && !$service->getUserAccess($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $isAlreadyInStaging)) {
            Log::warning('User attempted staging action without access', [
                'user_id' => auth()->user()->id,
                'menu_dtl_id' => $menuDtlId,
                'flow_id' => $currentFlowId,
                'document_id' => $formId
            ]);
            return;
        }

        $requestedCode = strtolower(trim((string) $request->input('staging_action_code', '')));
        $actionId = $request->current_actions_id ?? null;
        $actions = [];
        if ($currentFlowId) {
            $actions = $service->getFormActions($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $isAlreadyInStaging);
        }
        if ($currentFlowId) {
            if ($requestedCode !== '') {
                foreach ($actions as $action) {
                    $name = strtolower($action->stg_actions_name ?? '');
                    $orig = strtolower($action->original_action ?? $name);
                    if ($name === $requestedCode || $orig === $requestedCode) {
                        $actionId = $action->stg_actions_id;
                        break;
                    }
                }
            }
            if (empty($actionId) || trim((string) $actionId) === '') {
                foreach ($actions as $action) {
                    $name = $action->stg_actions_name ?? '';
                    $orig = $action->original_action ?? $name;
                    if (in_array($name, ['save', 'create', 'edit'], true) || in_array($orig, ['save', 'create', 'edit'], true)) {
                        $actionId = $action->stg_actions_id;
                        break;
                    }
                }
            }
        }

        if ($isNew && $currentFlowId && $model && (property_exists($model, 'current_stg_id') || $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'current_stg_id'))) {
            $model->current_stg_id = $currentFlowId;
        }

        if ($currentFlowId && $actionId) {
            $this->logStagingActivity(
                $menuDtlId,
                $formId,
                $currentFlowId,
                $actionId,
                $request->flow_remarks ?? null,
                0
            );

            $actionName = null;
            $originalAction = null;
            foreach ($actions as $action) {
                if ((string) $action->stg_actions_id === (string) $actionId) {
                    $actionName = $action->stg_actions_name;
                    $originalAction = $action->original_action ?? $actionName;
                    break;
                }
            }

            $flowsData = $service->getFormFlows($menuDtlId, $currentFlowId, $formIdForCriteria ?: $formId, $isAlreadyInStaging);
            $nextFlowId = isset($flowsData['next']) && $flowsData['next']
                ? (is_object($flowsData['next']) ? $flowsData['next']->stg_flows_id : $flowsData['next'])
                : null;
            $prevFlowId = isset($flowsData['prev']) && $flowsData['prev']
                ? (is_object($flowsData['prev']) ? $flowsData['prev']->stg_flows_id : $flowsData['prev'])
                : null;

            $isPostLike = in_array(strtolower($originalAction), ['post'], true)
                || in_array(strtolower($actionName), ['post'], true);
            $isForwardLike = in_array(strtolower($originalAction), ['forward'], true)
                || in_array(strtolower($actionName), ['forward'], true);
            $isBackLike = in_array(strtolower($originalAction), ['back'], true)
                || in_array(strtolower($actionName), ['back'], true);
            $isCancelLike = in_array(strtolower($originalAction), ['cancel'], true)
                || in_array(strtolower($actionName), ['cancel'], true);

            if ($isCancelLike) {
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
                if ($model && (property_exists($model, 'current_stg_id') ||
                    $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'current_stg_id'))) {
                    $model->current_stg_id = $nextFlowId;
                }
            } elseif ($isBackLike && $prevFlowId !== null && $prevFlowId !== '') {
                if ($model && (property_exists($model, 'current_stg_id') ||
                    $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'current_stg_id'))) {
                    $model->current_stg_id = $prevFlowId;
                }
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
                    $notificationConfig,
                    $model
                );
            }
        } elseif ($isNew) {
            $flows = $service->getFormFlows($menuDtlId, null, null);
            if (!empty($flows['all']) && $model &&
                (property_exists($model, 'current_stg_id') ||
                 $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'current_stg_id'))) {
                $model->current_stg_id = $flows['all'][0]->stg_flows_id;
            }
            if ($notificationConfig && !empty($notificationConfig['listing_view']) && isset($notificationConfig['form_path']) && !empty($flows['all'])) {
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
        array $notificationConfig,
        $model = null
    ) {
        $stage = 'Draft';
        $targetFlowId = $currentFlowId;

        if ($isCancelLike) {
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
}
