<?php

namespace App\Http\ViewComposers;

use App\Services\StagingService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;

class StagingComposer
{
    protected $stagingService;

    public function __construct()
    {
        $this->stagingService = new StagingService();
    }

    public function compose(View $view)
    {
        $viewName = $view->getName();
        $routeAction = Route::currentRouteAction() ?? '';
        $routeName = Route::currentRouteName() ?? '';
        $isFormView = strpos($viewName, 'form') !== false
            || strpos($routeAction, '@create') !== false
            || strpos($routeAction, '@edit') !== false
            || strpos($routeName, 'form') !== false
            || strpos($routeName, 'create') !== false
            || strpos($routeName, 'edit') !== false;

        if (!$isFormView) {
            return;
        }

        $data = $view->getData();
        $payload = isset($data['data']) && is_array($data['data']) ? $data['data'] : $data;
        $menuDtlId = $payload['menu_dtl_id'] ?? $payload['menu_id'] ?? $data['menu_dtl_id'] ?? $data['menu_id'] ?? null;
        if (!$menuDtlId) {
            return;
        }

        $current = $payload['current'] ?? $data['current'] ?? null;
        $formId = $payload['id'] ?? $data['id'] ?? null;
        if ($formId === null && $current && is_object($current) && method_exists($current, 'getKey')) {
            $formId = $current->getKey();
        }
        $currentFlowId = ($current && isset($current->current_stg_id)) ? $current->current_stg_id : null;

        // Only evaluate staging when document exists (criteria conditions may depend on saved values)
        if (!$formId) {
            return;
        }

        $isAlreadyInStaging = $current && !empty($current->current_stg_id) && (int)($current->posted ?? 0) === 0;
        $stagingExempt = $this->stagingService->isDocumentStagingExempt($current, $menuDtlId);
        $stagingEnrolled = $this->stagingService->isDocumentStagingEnrolled($current, $menuDtlId);
        $skipCriteriaConditions = $isAlreadyInStaging || $stagingEnrolled;
        $showStagingUi = false;
        if (!$stagingExempt && ($stagingEnrolled || $isAlreadyInStaging)) {
            if ($stagingEnrolled) {
                $criteria = $this->stagingService->getFlowCriteriaForForm(
                    $menuDtlId,
                    $formId,
                    $skipCriteriaConditions,
                    $current
                );
                $showStagingUi = $criteria !== null
                    && $this->stagingService->criteriaFlowsForDocument($criteria, $current, $menuDtlId)->isNotEmpty();
            } else {
                $showStagingUi = $this->stagingService->hasStagingOrRemainsInStaging(
                    $menuDtlId,
                    $formId,
                    true,
                    $current
                );
            }
        }

        if ($showStagingUi) {
            $flows = $this->stagingService->getFormFlows($menuDtlId, $currentFlowId, $formId, $skipCriteriaConditions, $current);
            $actions = [];
            $userAccess = false;
            $eligibleUsers = collect([]);

            if ($flows['current']) {
                $currentFlowId = $flows['current']->stg_flows_id;
                $actions = $this->stagingService->getFormActions($menuDtlId, $currentFlowId, $formId, $skipCriteriaConditions, $current);
                $userAccess = $this->stagingService->getUserAccess($menuDtlId, $currentFlowId, $formId, $skipCriteriaConditions, $current);
                $eligibleUsers = $this->stagingService->getEligibleUsers($menuDtlId, $currentFlowId, $formId, $skipCriteriaConditions, $current);
            }

            $postedState = ($current && isset($current->posted)) ? (int) $current->posted : 0;
            $unpostUserAccess = false;
            if (in_array($postedState, [1, 2], true)) {
                $accessibleUnpostActions = $this->stagingService->collectAccessibleUnpostActionsForDocument(
                    $menuDtlId,
                    $formId,
                    $skipCriteriaConditions,
                    $current
                );
                $unpostUserAccess = !empty($accessibleUnpostActions);
                $existingActionIds = [];
                foreach ($actions as $action) {
                    $existingActionIds[(string) ($action->stg_actions_id ?? '')] = true;
                }
                foreach ($accessibleUnpostActions as $unpostAction) {
                    $actionId = (string) ($unpostAction->stg_actions_id ?? '');
                    if ($actionId === '' || !isset($existingActionIds[$actionId])) {
                        $actions[] = $unpostAction;
                        if ($actionId !== '') {
                            $existingActionIds[$actionId] = true;
                        }
                    }
                }
            }

            $stagingData = [
                'has_staging' => true,
                'flows' => $flows,
                'actions' => $actions,
                'user_access' => $userAccess,
                'unpost_user_access' => $unpostUserAccess,
                'eligible_users' => $eligibleUsers
            ];

            $view->with('staging_data', $stagingData);
            $view->with('staging_menu_dtl_id', $menuDtlId);
            $view->with('staging_form_id', $formId);

            if ($flows['current']) {
                $view->with('staging_flow_dtls', [
                    'current' => $flows['current'],
                    'next' => $flows['next'],
                    'prev' => $flows['prev'],
                    'all' => $flows['all']
                ]);
                $view->with('staging_last_flow', !empty($flows['all']) ? end($flows['all'])->stg_flows_id : null);
                $view->with('staging_first_flow', !empty($flows['all']) ? $flows['all'][0]->stg_flows_id : null);
            }

            if ($formId) {
                $activity = \App\Models\TblStgFormLog::with('flow_dtl', 'action_btn_dtl', 'user', 'criteria_action')
                    ->where('menu_dtl_id', $menuDtlId)
                    ->where('document_id', $formId)
                    ->orderBy('created_at', 'desc')
                    ->get();
                $view->with('staging_activity', $activity);
            }
        } else {
            $activity = \App\Models\TblStgFormLog::with('flow_dtl', 'action_btn_dtl', 'user', 'criteria_action')
                ->where('menu_dtl_id', $menuDtlId)
                ->where('document_id', $formId)
                ->orderBy('created_at', 'desc')
                ->get();
            if ($activity->isNotEmpty()) {
                $view->with('staging_activity', $activity);
                $view->with('staging_menu_dtl_id', $menuDtlId);
                $view->with('staging_form_id', $formId);
                $view->with('staging_activity_only', true);
            }
        }
    }
}
