<?php

namespace App\Services;

use App\Models\TblMenuFlowCriteria;
use App\Models\TblMenuFlowCriteriaFlow;
use App\Models\TblMenuFlowCriteriaFlowAction;
use App\Models\TblMenuFlowCriteriaFlowUser;
use App\Models\TblMenuFlowCriteriaFlowDesignation;
use App\Models\TblSoftMenuDtl;
use App\Models\TblStgFlows;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StagingService
{
    protected $stagingDefinedMenuDtlIds = null;

    protected $dashboardCache = [
        'menu_dtl' => [],
        'has_enrolled' => [],
        'enrolled_flow_ids' => [],
        'criteria_dashboard' => [],
        'criteria_flows_dashboard' => [],
        'user_access_dashboard' => [],
    ];

    protected function dashboardMenuKey($formNameOrMenuDtlId): string
    {
        if (is_numeric($formNameOrMenuDtlId)) {
            return (string) $formNameOrMenuDtlId;
        }
        return strtolower(trim((string) $formNameOrMenuDtlId));
    }

    protected function resolveMenuDtl($formNameOrMenuDtlId)
    {
        $key = $this->dashboardMenuKey($formNameOrMenuDtlId);
        if (array_key_exists($key, $this->dashboardCache['menu_dtl'])) {
            return $this->dashboardCache['menu_dtl'][$key];
        }

        $menu = is_numeric($formNameOrMenuDtlId)
            ? TblSoftMenuDtl::find($formNameOrMenuDtlId)
            : TblSoftMenuDtl::where('menu_dtl_name', $formNameOrMenuDtlId)->first();

        $this->dashboardCache['menu_dtl'][$key] = $menu;

        return $menu;
    }

    protected function flowIsEnabled($flow): bool
    {
        if (!$flow) {
            return false;
        }

        $candidates = [
            'menu_flow_criteria_flow_entry_status',
            'menu_flow_criteria_flow_status',
            'flow_entry_status',
            'flow_status',
            'entry_status',
            'status',
            'is_active',
            'active',
            'enabled',
        ];

        foreach ($candidates as $key) {
            if (isset($flow->$key)) {
                return (int) $flow->$key === 1;
            }
        }

        // If there is no explicit enable/disable column on tbl_menu_flow_* flow rows,
        // treat the flow as enabled.
        return true;
    }

    protected function activeCriteriaFlows($criteria)
    {
        if (!$criteria || !$criteria->flows || $criteria->flows->isEmpty()) {
            return collect([]);
        }
        return $criteria->flows
            ->filter(fn ($f) => $this->flowIsEnabled($f))
            ->values();
    }

    public function criteriaFlowsForDocument($criteria, $model = null, $formNameOrMenuDtlId = null)
    {
        if (!$criteria || !$criteria->flows || $criteria->flows->isEmpty()) {
            return collect([]);
        }

        $criteriaInactive = (int) ($criteria->menu_flow_criteria_status ?? 0) !== 1
            || (int) ($criteria->menu_flow_criteria_entry_status ?? 0) !== 1;

        if ($criteriaInactive && $this->isDocumentStagingEnrolled($model, $formNameOrMenuDtlId)) {
            return $criteria->flows->values();
        }

        return $this->activeCriteriaFlows($criteria);
    }

    public function menuHasEnrolledStagingDocuments($formNameOrMenuDtlId): bool
    {
        $key = $this->dashboardMenuKey($formNameOrMenuDtlId);
        if (array_key_exists($key, $this->dashboardCache['has_enrolled'])) {
            return (bool) $this->dashboardCache['has_enrolled'][$key];
        }

        $menu = $this->resolveMenuDtl($formNameOrMenuDtlId);

        if (!$menu || !$menu->menu_dtl_table_name) {
            $this->dashboardCache['has_enrolled'][$key] = false;
            return false;
        }

        if (!$this->tableHasStagingWorkflowColumns($menu->menu_dtl_table_name)) {
            $this->dashboardCache['has_enrolled'][$key] = false;
            return false;
        }

        $query = DB::table($menu->menu_dtl_table_name)
            ->where('staging_apply', $this->getStagingApplyEnrolledValue($formNameOrMenuDtlId))
            ->where('posted', 0);

        $this->scopeAccoVoucherByMenu($formNameOrMenuDtlId, $menu->menu_dtl_table_name, $query);

        $result = $query->exists();
        $this->dashboardCache['has_enrolled'][$key] = (bool) $result;

        return (bool) $result;
    }

    public function getFlowCriteriaForDashboard($formNameOrMenuDtlId)
    {
        $key = $this->dashboardMenuKey($formNameOrMenuDtlId);
        if (array_key_exists($key, $this->dashboardCache['criteria_dashboard'])) {
            return $this->dashboardCache['criteria_dashboard'][$key];
        }

        if (is_numeric($formNameOrMenuDtlId)) {
            $formName = $this->getFormNameFromMenuDtlId($formNameOrMenuDtlId);
            if (!$formName) {
                $this->dashboardCache['criteria_dashboard'][$key] = null;
                return null;
            }
        } else {
            $formName = trim($formNameOrMenuDtlId);
        }

        $hasEnrolledDocuments = $this->menuHasEnrolledStagingDocuments($formNameOrMenuDtlId);

        $criteria = $this->flowCriteriaQuery($formNameOrMenuDtlId, $formName, true)
            ->orderBy('menu_flow_criteria_apply_at', 'desc')
            ->first();

        if (!$criteria && $hasEnrolledDocuments) {
            $criteria = $this->flowCriteriaQuery($formNameOrMenuDtlId, $formName, false)
                ->orderBy('menu_flow_criteria_apply_at', 'desc')
                ->first();
        }

        if (!$criteria) {
            $this->dashboardCache['criteria_dashboard'][$key] = null;
            return null;
        }

        $criteriaInactive = (int) ($criteria->menu_flow_criteria_status ?? 0) !== 1
            || (int) ($criteria->menu_flow_criteria_entry_status ?? 0) !== 1;

        if ($criteriaInactive && !$hasEnrolledDocuments) {
            $this->dashboardCache['criteria_dashboard'][$key] = null;
            return null;
        }

        $this->dashboardCache['criteria_dashboard'][$key] = $criteria;
        return $criteria;
    }

    protected function getEnrolledStagingFlowIds($formNameOrMenuDtlId): array
    {
        $key = $this->dashboardMenuKey($formNameOrMenuDtlId);
        if (array_key_exists($key, $this->dashboardCache['enrolled_flow_ids'])) {
            return $this->dashboardCache['enrolled_flow_ids'][$key];
        }

        $menu = $this->resolveMenuDtl($formNameOrMenuDtlId);

        if (!$menu || !$menu->menu_dtl_table_name || !$this->tableHasStagingWorkflowColumns($menu->menu_dtl_table_name)) {
            $this->dashboardCache['enrolled_flow_ids'][$key] = [];
            return [];
        }

        $query = DB::table($menu->menu_dtl_table_name)
            ->where('staging_apply', $this->getStagingApplyEnrolledValue($formNameOrMenuDtlId))
            ->where('posted', 0)
            ->whereNotNull('current_stg_id');

        $this->scopeAccoVoucherByMenu($formNameOrMenuDtlId, $menu->menu_dtl_table_name, $query);

        $ids = $query->distinct()
            ->pluck('current_stg_id')
            ->filter(function ($id) {
                return $id !== null && $id !== '';
            })
            ->values()
            ->all();
        $this->dashboardCache['enrolled_flow_ids'][$key] = $ids;

        return $ids;
    }

    public function criteriaFlowsForDashboard($criteria, $formNameOrMenuDtlId = null)
    {
        if (!$criteria || !$criteria->flows || $criteria->flows->isEmpty()) {
            return collect([]);
        }

        $key = $this->dashboardMenuKey($formNameOrMenuDtlId);
        $criteriaKey = (string) ($criteria->menu_flow_criteria_id ?? '0');
        $cacheKey = $key . ':' . $criteriaKey;
        if (array_key_exists($cacheKey, $this->dashboardCache['criteria_flows_dashboard'])) {
            return $this->dashboardCache['criteria_flows_dashboard'][$cacheKey];
        }

        $criteriaInactive = (int) ($criteria->menu_flow_criteria_status ?? 0) !== 1
            || (int) ($criteria->menu_flow_criteria_entry_status ?? 0) !== 1;

        if ($criteriaInactive && $this->menuHasEnrolledStagingDocuments($formNameOrMenuDtlId)) {
            $result = $criteria->flows->values();
            $this->dashboardCache['criteria_flows_dashboard'][$cacheKey] = $result;
            return $result;
        }

        $result = $this->activeCriteriaFlows($criteria);
        $this->dashboardCache['criteria_flows_dashboard'][$cacheKey] = $result;
        return $result;
    }

    public function hasStagingDashboardForMenu($formNameOrMenuDtlId): bool
    {
        if (!$this->menuHasStagingDefined($formNameOrMenuDtlId)) {
            return false;
        }

        if ($this->hasStaging($formNameOrMenuDtlId)) {
            return true;
        }

        if (!$this->menuHasEnrolledStagingDocuments($formNameOrMenuDtlId)) {
            return false;
        }

        $criteria = $this->getFlowCriteriaForDashboard($formNameOrMenuDtlId);

        return $criteria !== null
            && $this->criteriaFlowsForDashboard($criteria, $formNameOrMenuDtlId)->isNotEmpty();
    }

    public function getStagingDashboardMenuDtlIds(): array
    {
        if ($this->stagingDefinedMenuDtlIds !== null) {
            return $this->stagingDefinedMenuDtlIds;
        }

        $menuDtlIds = TblMenuFlowCriteria::query()
            ->whereNotNull('menu_dtl_id')
            ->distinct()
            ->pluck('menu_dtl_id')
            ->filter(function ($id) {
                return $id !== null && $id !== '' && (int) $id > 0;
            })
            ->map(function ($id) {
                return (int) $id;
            })
            ->values()
            ->all();

        $criteriaNames = TblMenuFlowCriteria::query()
            ->where(function ($q) {
                $q->whereNull('menu_dtl_id')->orWhere('menu_dtl_id', 0);
            })
            ->whereNotNull('menu_flow_criteria_name')
            ->distinct()
            ->pluck('menu_flow_criteria_name')
            ->filter()
            ->map(function ($name) {
                return strtolower(trim((string) $name));
            })
            ->unique()
            ->values()
            ->all();

        if (!empty($criteriaNames)) {
            $menusByName = TblSoftMenuDtl::query()
                ->where(function ($q) use ($criteriaNames) {
                    foreach ($criteriaNames as $name) {
                        $q->orWhereRaw('lower(trim(menu_dtl_name)) = ?', [$name]);
                    }
                })
                ->pluck('menu_dtl_id')
                ->map(function ($id) {
                    return (int) $id;
                })
                ->all();

            $menuDtlIds = array_values(array_unique(array_merge($menuDtlIds, $menusByName)));
        }

        $this->stagingDefinedMenuDtlIds = $menuDtlIds;

        return $menuDtlIds;
    }

    public function getMenusForStagingDashboard()
    {
        $menuDtlIds = $this->getStagingDashboardMenuDtlIds();
        if (empty($menuDtlIds)) {
            return TblSoftMenuDtl::query()->whereRaw('1 = 0')->get();
        }

        return TblSoftMenuDtl::whereIn('menu_dtl_id', $menuDtlIds)->get();
    }

    public function menuHasStagingDefined($formNameOrMenuDtlId): bool
    {
        if (is_numeric($formNameOrMenuDtlId)) {
            return in_array((int) $formNameOrMenuDtlId, $this->getStagingDashboardMenuDtlIds(), true);
        }

        $menu = $this->resolveMenuDtl($formNameOrMenuDtlId);

        return $menu
            ? in_array((int) $menu->menu_dtl_id, $this->getStagingDashboardMenuDtlIds(), true)
            : false;
    }

    protected function getFormNameFromMenuDtlId($menuDtlId)
    {
        $menu = TblSoftMenuDtl::find($menuDtlId);
        return $menu ? $menu->menu_dtl_name : null;
    }


    public function documentRetainsStagingWorkflow($model, $isAlreadyInStaging = false, $formNameOrMenuDtlId = null): bool
    {
        return $isAlreadyInStaging || $this->isDocumentStagingEnrolled($model, $formNameOrMenuDtlId);
    }

    protected function flowCriteriaQuery($formNameOrMenuDtlId, $formName, $activeOnly = true)
    {
        $criteriaQuery = TblMenuFlowCriteria::with(['flows.actions', 'flows.users', 'flows.designations', 'conditions']);
        if ($activeOnly) {
            $criteriaQuery->active();
        }

        if (is_numeric($formNameOrMenuDtlId)) {
            $criteriaQuery->where(function ($q) use ($formNameOrMenuDtlId, $formName) {
                $q->where('menu_dtl_id', $formNameOrMenuDtlId)
                  ->orWhereRaw('lower(trim(menu_flow_criteria_name)) = ?', [strtolower(trim($formName))]);
            });
        } else {
            $criteriaQuery->forForm($formName);
        }

        return $criteriaQuery;
    }

    public function getFlowCriteriaForForm($formNameOrMenuDtlId, $formId = null, $skipConditionCheck = false, $model = null)
    {
        if (is_numeric($formNameOrMenuDtlId)) {
            $formName = $this->getFormNameFromMenuDtlId($formNameOrMenuDtlId);
            if (!$formName) {
                return null;
            }
        } else {
            $formName = $formNameOrMenuDtlId;
        }
        $formName = trim($formName);

        $retainForEnrolled = $this->isDocumentStagingEnrolled($model, $formNameOrMenuDtlId);
        $criteria = $this->flowCriteriaQuery($formNameOrMenuDtlId, $formName, true)
            ->orderBy('menu_flow_criteria_apply_at', 'desc')
            ->first();
        $usedInactiveCriteria = false;

        if (!$criteria && $retainForEnrolled) {
            $criteria = $this->flowCriteriaQuery($formNameOrMenuDtlId, $formName, false)
                ->orderBy('menu_flow_criteria_apply_at', 'desc')
                ->first();
            $usedInactiveCriteria = (bool) $criteria;
        }

        if (!$criteria) {
            return null;
        }

        if (!$usedInactiveCriteria
            && ($criteria->menu_flow_criteria_status != 1 || $criteria->menu_flow_criteria_entry_status != 1)) {
            return null;
        }

        if ($formId && $criteria->conditions->count() > 0 && !$skipConditionCheck) {

            $menu = TblSoftMenuDtl::where('menu_dtl_name', $formName)->first();
            $tableName = $menu ? $menu->menu_dtl_table_name : null;
            $menuDtlIdForPk = is_numeric($formNameOrMenuDtlId)
                ? (string) $formNameOrMenuDtlId
                : ($menu ? (string) $menu->menu_dtl_id : null);
            $configuredDocumentPk = $menuDtlIdForPk
                ? config('staging.document_primary_keys_by_menu.' . $menuDtlIdForPk)
                : null;

            if ($tableName && !$this->evaluateCriteriaConditions($criteria, $tableName, $formId, $configuredDocumentPk, $formNameOrMenuDtlId)) {
                return null;
            }
        }

        return $criteria;
    }

    public function evaluateCriteriaConditions($criteria, $formTableName, $formId, $configuredPrimaryKey = null, $formNameOrMenuDtlId = null)
    {
        $conditions = $criteria->conditions;

        if ($conditions->isEmpty()) {
            return true;
        }

        $whereClause = '';
        $bindings = [];
        $logicOperator = null;

        foreach ($conditions as $index => $condition) {
            if ($index > 0) {
                $operator = !empty($condition->condition_logic_operator)
                    ? strtoupper(trim($condition->condition_logic_operator))
                    : 'AND';
                $whereClause .= ' ' . $operator . ' ';
            }

            $field = $condition->condition_field;
            $operator = $condition->condition_operator;
            $value = $condition->condition_value;

            switch ($operator) {
                case '=':
                case '!=':
                case '>':
                case '<':
                case '>=':
                case '<=':
                    $whereClause .= "{$field} {$operator} ?";
                    $bindings[] = $value;
                    break;
                case 'Like':
                    $whereClause .= "UPPER({$field}) LIKE UPPER(?)";
                    $bindings[] = "%{$value}%";
                    break;
                case 'Between':
                    $values = explode(',', $value);
                    if (count($values) == 2) {
                        $whereClause .= "{$field} BETWEEN ? AND ?";
                        $bindings[] = trim($values[0]);
                        $bindings[] = trim($values[1]);
                    }
                    break;
            }
        }

        if (empty($whereClause)) {
            return true;
        }

        $columns = DB::getSchemaBuilder()->getColumnListing($formTableName);
        if (empty($columns)) {
            return true;
        }

        $primaryKey = null;
        if ($configuredPrimaryKey !== null && $configuredPrimaryKey !== '') {
            foreach ($columns as $col) {
                if (strtolower($col) === strtolower($configuredPrimaryKey)) {
                    $primaryKey = $col;
                    break;
                }
            }
            if ($primaryKey === null) {
                return false;
            }
            $query = DB::table($formTableName);
            $query->where($primaryKey, $formId);
            $this->scopeAccoVoucherForConditionCheck($formNameOrMenuDtlId, $formTableName, $query);
            return $query->whereRaw($whereClause, $bindings)->exists();
        }

        $rawSegments = explode('_', strtolower($formTableName));

        $segments = $rawSegments;
        while (!empty($segments) && in_array($segments[0], ['tbl', 'vw', 'v'], true)) {
            array_shift($segments);
        }
        while (!empty($segments) && in_array(end($segments), ['listing', 'list', 'view'], true)) {
            array_pop($segments);
        }

        $possibleKeys = [
            strtolower($formTableName) . '_id',
            str_replace('tbl_', '', strtolower($formTableName)) . '_id',
            'id',
        ];

        if (count($rawSegments) >= 2) {
            $possibleKeys[] = implode('_', array_slice($rawSegments, -2)) . '_id';
        }

        if (count($segments) >= 2) {
            $possibleKeys[] = implode('_', $segments) . '_id';
            $possibleKeys[] = implode('_', array_slice($segments, -2)) . '_id';
        }

        if (count($segments) >= 3) {
            $possibleKeys[] = implode('_', array_slice($segments, -3)) . '_id';
        }

        if (count($segments) >= 3 && strlen((string) $segments[0]) <= 4) {
            $noPrefix = array_slice($segments, 1);
            if (count($noPrefix) >= 2) {
                $possibleKeys[] = implode('_', $noPrefix) . '_id';
                $possibleKeys[] = implode('_', array_slice($noPrefix, -2)) . '_id';
            }
            if (count($noPrefix) >= 3) {
                $possibleKeys[] = implode('_', array_slice($noPrefix, -3)) . '_id';
            }
        }

        $possibleKeys = array_values(array_unique(array_filter($possibleKeys)));

        $primaryKey = null;
        foreach ($possibleKeys as $key) {
            foreach ($columns as $col) {
                if (strtolower($col) === strtolower($key)) {
                    $primaryKey = $col;
                    break 2;
                }
            }
        }

        if ($primaryKey === null && $formId !== null) {
            return false;
        }

        $query = DB::table($formTableName);
        if ($primaryKey !== null && $formId !== null) {
            $query->where($primaryKey, $formId);
        }

        $this->scopeAccoVoucherForConditionCheck($formNameOrMenuDtlId, $formTableName, $query);

        $result = $query->whereRaw($whereClause, $bindings)->exists();

        return $result;
    }

    public function getFormFlows($formNameOrMenuDtlId, $currentFlowId = null, $formId = null, $skipConditionCheck = false, $model = null)
    {
        $criteria = $this->getFlowCriteriaForForm($formNameOrMenuDtlId, $formId, $skipConditionCheck, $model);
        $activeFlows = $this->criteriaFlowsForDocument($criteria, $model, $formNameOrMenuDtlId);
        if (!$criteria || $activeFlows->isEmpty()) {
            return [
                'all' => [],
                'current' => null,
                'next' => null,
                'prev' => null
            ];
        }

        $sortedFlows = $activeFlows->sortBy(function ($flow) {
            return (int) ($flow->flow_order ?? 0);
        })->values();

        $flows = [];
        $currentFlow = null;
        $currentIndex = -1;

        foreach ($sortedFlows as $flow) {
            $flowObj = (object)[
                'stg_flows_id' => $flow->stg_flows_id,
                'stg_flows_name' => $flow->flow_name ?: 'Unknown',
                'flow_order' => $flow->flow_order
            ];
            $flows[] = $flowObj;

            if ($currentFlowId && (string) $flow->stg_flows_id === (string) $currentFlowId) {
                $currentFlow = $flowObj;
                $currentIndex = count($flows) - 1;
            }
        }

        if (!$currentFlow && !empty($flows)) {
            $currentFlow = $flows[0];
            $currentIndex = 0;
        }

        $nextFlow = isset($flows[$currentIndex + 1]) ? $flows[$currentIndex + 1] : null;
        $prevFlow = isset($flows[$currentIndex - 1]) ? $flows[$currentIndex - 1] : null;

        return [
            'all' => $flows,
            'current' => $currentFlow,
            'next' => $nextFlow,
            'prev' => $prevFlow
        ];
    }

    public function getFormFlowsForDashboard($formNameOrMenuDtlId, $currentFlowId = null)
    {
        $criteria = $this->getFlowCriteriaForDashboard($formNameOrMenuDtlId);
        $activeFlows = $this->criteriaFlowsForDashboard($criteria, $formNameOrMenuDtlId);
        if (!$criteria || $activeFlows->isEmpty()) {
            return [
                'all' => [],
                'current' => null,
                'next' => null,
                'prev' => null,
            ];
        }

        $sortedFlows = $activeFlows->sortBy(function ($flow) {
            return (int) ($flow->flow_order ?? 0);
        })->values();

        $flows = [];
        $currentFlow = null;
        $currentIndex = -1;

        foreach ($sortedFlows as $flow) {
            $flowObj = (object) [
                'stg_flows_id' => $flow->stg_flows_id,
                'stg_flows_name' => $flow->flow_name ?: 'Unknown',
                'flow_order' => $flow->flow_order,
            ];
            $flows[] = $flowObj;

            if ($currentFlowId && (string) $flow->stg_flows_id === (string) $currentFlowId) {
                $currentFlow = $flowObj;
                $currentIndex = count($flows) - 1;
            }
        }

        $knownFlowIds = collect($flows)->map(function ($f) {
            return (string) $f->stg_flows_id;
        })->all();
        $missingFlowIds = array_values(array_filter(
            $this->getEnrolledStagingFlowIds($formNameOrMenuDtlId),
            function ($flowId) use ($knownFlowIds) {
                return !in_array((string) $flowId, $knownFlowIds, true);
            }
        ));
        if (!empty($missingFlowIds)) {
            $stageFlows = TblStgFlows::whereIn('stg_flows_id', $missingFlowIds)->get()->keyBy(function ($row) {
                return (string) $row->stg_flows_id;
            });
            foreach ($missingFlowIds as $flowId) {
                $stageFlow = $stageFlows->get((string) $flowId);
                $flows[] = (object) [
                    'stg_flows_id' => $flowId,
                    'stg_flows_name' => $stageFlow && $stageFlow->stg_flows_name
                        ? $stageFlow->stg_flows_name
                        : 'Unknown',
                    'flow_order' => 9999,
                ];
            }
        }

        if (!$currentFlow && !empty($flows)) {
            $currentFlow = $flows[0];
            $currentIndex = 0;
        }

        $nextFlow = isset($flows[$currentIndex + 1]) ? $flows[$currentIndex + 1] : null;
        $prevFlow = isset($flows[$currentIndex - 1]) ? $flows[$currentIndex - 1] : null;

        return [
            'all' => $flows,
            'current' => $currentFlow,
            'next' => $nextFlow,
            'prev' => $prevFlow,
        ];
    }

    public function getFormActions($formNameOrMenuDtlId, $flowId, $formId = null, $skipConditionCheck = false, $model = null)
    {
        $criteria = $this->getFlowCriteriaForForm($formNameOrMenuDtlId, $formId, $skipConditionCheck, $model);

        if (!$criteria || !$flowId) {
            return [];
        }

        $flow = $this->criteriaFlowsForDocument($criteria, $model, $formNameOrMenuDtlId)
            ->first(function ($f) use ($flowId) {
                return (string) $f->stg_flows_id === (string) $flowId;
            });

        if (!$flow) {
            return [];
        }

        $flowActions = $flow->actions;
        if (!$flowActions || $flowActions->isEmpty()) {
            $flowActions = TblMenuFlowCriteriaFlowAction::where('menu_flow_criteria_flow_id', $flow->menu_flow_criteria_flow_id)->get();
        }

        $actionMap = [
            'create' => 'save',
            'edit' => 'save',
            'save' => 'save',
            'forward' => 'forward',
            'send_forward' => 'forward',
            'post' => 'post',
            'back' => 'back',
            'send_back' => 'back',
            'sendback' => 'back',
            'cancel' => 'cancel',
            'un_post' => 'un_post',
            'unpost' => 'un_post',
        ];

        $actions = [];
        $seenCanonical = [];
        foreach ($flowActions as $flowAction) {
            $actionName = $flowAction->action_name;
            $actionKey = strtolower(str_replace([' ', '-'], '_', trim((string) $actionName)));
            $canonicalAction = $actionMap[$actionKey] ?? $actionKey;

            if (isset($seenCanonical[$canonicalAction])) {
                continue;
            }
            $seenCanonical[$canonicalAction] = true;

            $actions[] = (object)[
                'stg_actions_id' => $flowAction->menu_flow_criteria_flow_action_id,
                'stg_actions_name' => $actionName,
                'stg_actions_label' => ucfirst(str_replace('_', ' ', $actionName)),
                'original_action' => $canonicalAction,
            ];
        }

        return $actions;
    }

    protected function isUnpostLikeAction($action): bool
    {
        $orig = strtolower(str_replace([' ', '-'], '_', trim((string) ($action->original_action ?? $action->stg_actions_name ?? ''))));

        return in_array($orig, ['un_post', 'unpost'], true);
    }

    public function collectUnpostActionsForDocument($formNameOrMenuDtlId, $formId = null, $skipConditionCheck = false, $model = null): array
    {
        $criteria = $this->getFlowCriteriaForForm($formNameOrMenuDtlId, $formId, $skipConditionCheck, $model);
        if (!$criteria) {
            return [];
        }

        $unpostActions = [];
        $seenIds = [];

        foreach ($this->criteriaFlowsForDocument($criteria, $model, $formNameOrMenuDtlId) as $flow) {
            $flowActions = $this->getFormActions(
                $formNameOrMenuDtlId,
                $flow->stg_flows_id,
                $formId,
                $skipConditionCheck,
                $model
            );
            foreach ($flowActions as $action) {
                if (!$this->isUnpostLikeAction($action)) {
                    continue;
                }
                $actionId = (string) ($action->stg_actions_id ?? '');
                if ($actionId !== '' && isset($seenIds[$actionId])) {
                    continue;
                }
                if ($actionId !== '') {
                    $seenIds[$actionId] = true;
                }
                $unpostActions[] = $action;
            }
        }

        return $unpostActions;
    }

    public function collectAccessibleUnpostActionsForDocument($formNameOrMenuDtlId, $formId = null, $skipConditionCheck = false, $model = null): array
    {
        $criteria = $this->getFlowCriteriaForForm($formNameOrMenuDtlId, $formId, $skipConditionCheck, $model);
        if (!$criteria) {
            return [];
        }

        $unpostActions = [];
        $seenIds = [];

        foreach ($this->criteriaFlowsForDocument($criteria, $model, $formNameOrMenuDtlId) as $flow) {
            if (!$this->getUserAccess($formNameOrMenuDtlId, $flow->stg_flows_id, $formId, $skipConditionCheck, $model)) {
                continue;
            }

            $flowActions = $this->getFormActions(
                $formNameOrMenuDtlId,
                $flow->stg_flows_id,
                $formId,
                $skipConditionCheck,
                $model
            );
            foreach ($flowActions as $action) {
                if (!$this->isUnpostLikeAction($action)) {
                    continue;
                }
                $actionId = (string) ($action->stg_actions_id ?? '');
                if ($actionId !== '' && isset($seenIds[$actionId])) {
                    continue;
                }
                if ($actionId !== '') {
                    $seenIds[$actionId] = true;
                }
                $unpostActions[] = $action;
            }
        }

        return $unpostActions;
    }

    public function userHasUnpostAccessForDocument($formNameOrMenuDtlId, $formId = null, $skipConditionCheck = false, $model = null): bool
    {
        return !empty($this->collectAccessibleUnpostActionsForDocument($formNameOrMenuDtlId, $formId, $skipConditionCheck, $model));
    }

    public function getUserAccess($formNameOrMenuDtlId, $flowId, $formId = null, $skipConditionCheck = false, $model = null)
    {
        $criteria = $this->getFlowCriteriaForForm($formNameOrMenuDtlId, $formId, $skipConditionCheck, $model);

        if (!$criteria || !$flowId) {
            return false;
        }

        $flow = $this->criteriaFlowsForDocument($criteria, $model, $formNameOrMenuDtlId)
            ->first(function ($f) use ($flowId) {
                return (string) $f->stg_flows_id === (string) $flowId;
            });

        return $this->userHasAccessToCriteriaFlow($flow);
    }

    public function getUserAccessForDashboard($formNameOrMenuDtlId, $flowId)
    {
        if (!$flowId) {
            return false;
        }

        $menuKey = $this->dashboardMenuKey($formNameOrMenuDtlId);
        $accessKey = $menuKey . ':' . (string) $flowId;
        if (array_key_exists($accessKey, $this->dashboardCache['user_access_dashboard'])) {
            return (bool) $this->dashboardCache['user_access_dashboard'][$accessKey];
        }

        $criteria = $this->getFlowCriteriaForDashboard($formNameOrMenuDtlId);
        if (!$criteria) {
            $this->dashboardCache['user_access_dashboard'][$accessKey] = false;
            return false;
        }

        $flow = $this->criteriaFlowsForDashboard($criteria, $formNameOrMenuDtlId)
            ->first(function ($f) use ($flowId) {
                return (string) $f->stg_flows_id === (string) $flowId;
            });

        if (!$flow && $criteria->flows) {
            $flow = $criteria->flows->first(function ($f) use ($flowId) {
                return (string) $f->stg_flows_id === (string) $flowId;
            });
        }

        $result = $this->userHasAccessToCriteriaFlow($flow);
        $this->dashboardCache['user_access_dashboard'][$accessKey] = (bool) $result;

        return (bool) $result;
    }

    protected function userHasAccessToCriteriaFlow($flow): bool
    {
        if (!$flow) {
            return false;
        }

        $userId = (string) auth()->user()->id;

        $explicitUsers = $flow->users->pluck('user_id')->map(function ($id) {
            return (string) $id;
        })->toArray();
        if (in_array($userId, $explicitUsers, true)) {
            return true;
        }

        $designationIds = $flow->designations->pluck('designation_id')->filter()->values()->toArray();
        if (!empty($designationIds)) {
            $user = auth()->user();
            $designationIdsStr = array_map(fn ($id) => (string) $id, $designationIds);

            $employeeRoleId = $user->employee_role_id ?? null;
            if ($employeeRoleId && in_array((string) $employeeRoleId, $designationIdsStr, true)) {
                return true;
            }

            $hasRoleViaRelationship = Role::whereIn('id', $designationIds)
                ->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->exists();
            if ($hasRoleViaRelationship) {
                return true;
            }
            $hasRoleViaPivot = DB::table(config('laratrust.tables.role_user', 'role_user'))
                ->where('user_id', $user->id)
                ->whereIn('role_id', $designationIds)
                ->where('user_type', User::class)
                ->exists();
            if ($hasRoleViaPivot) {
                return true;
            }
        }

        if (empty($explicitUsers) && empty($designationIds)) {
            return true;
        }

        return false;
    }

    public function getEligibleUsers($formNameOrMenuDtlId, $flowId, $formId = null, $skipConditionCheck = false, $model = null)
    {
        $criteria = $this->getFlowCriteriaForForm($formNameOrMenuDtlId, $formId, $skipConditionCheck, $model);

        if (!$criteria || !$flowId) {
            return collect([]);
        }

        $flow = $this->criteriaFlowsForDocument($criteria, $model, $formNameOrMenuDtlId)
            ->first(function ($f) use ($flowId) {
                return (string) $f->stg_flows_id === (string) $flowId;
            });

        if (!$flow) {
            return collect([]);
        }

        $userIds = [];

        $explicitUserIds = $flow->users->pluck('user_id')->toArray();
        $userIds = array_merge($userIds, $explicitUserIds);

        $designationIds = $flow->designations->pluck('designation_id')->filter()->values()->toArray();
        if (!empty($designationIds)) {
            $query = User::where(function ($q) use ($designationIds) {
                $q->whereIn('employee_role_id', $designationIds)
                    ->orWhereHas('roles', function ($rq) use ($designationIds) {
                        $rq->whereIn('roles.id', $designationIds);
                    });
            });
            $designationUserIds = $query->pluck('id')->toArray();
            $userIds = array_merge($userIds, $designationUserIds);
        }

        $userIds = array_unique($userIds);

        if (empty($userIds)) {
            return collect([]);
        }

        return User::whereIn('id', $userIds)->get();
    }

    public function hasStaging($formNameOrMenuDtlId, $formId = null)
    {
        $criteria = $this->getFlowCriteriaForForm($formNameOrMenuDtlId, $formId);
        if ($criteria === null) {
            return false;
        }
        return $this->activeCriteriaFlows($criteria)->isNotEmpty();
    }

    public function hasStagingOrRemainsInStaging($formNameOrMenuDtlId, $formId, $forExistingDocument, $model = null)
    {
        if ($forExistingDocument || $this->isDocumentStagingEnrolled($model, $formNameOrMenuDtlId)) {
            $criteria = $this->getFlowCriteriaForForm($formNameOrMenuDtlId, null, true, $model);
            if ($criteria === null) {
                return false;
            }
            return $this->criteriaFlowsForDocument($criteria, $model, $formNameOrMenuDtlId)->isNotEmpty();
        }
        return $this->hasStaging($formNameOrMenuDtlId, $formId);
    }

    public function getStagingApplyEnrolledValue($formNameOrMenuDtlId = null): int
    {
        return 1;
    }

    public function getStagingApplyExemptValue($formNameOrMenuDtlId = null): int
    {
        return 0;
    }

    public function isDocumentStagingEnrolled($model, $formNameOrMenuDtlId = null): bool
    {
        if (!$model) {
            return false;
        }
        $enrolledValue = $this->getStagingApplyEnrolledValue($formNameOrMenuDtlId);

        return isset($model->staging_apply) && (int) $model->staging_apply === $enrolledValue;
    }

    public function isDocumentStagingExempt($model, $formNameOrMenuDtlId = null): bool
    {
        if (!$model) {
            return true;
        }
        $exemptValue = $this->getStagingApplyExemptValue($formNameOrMenuDtlId);

        return (int) ($model->staging_apply ?? $exemptValue) === $exemptValue;
    }

    public function shouldUseStagingForDocument($formNameOrMenuDtlId, $formId, $model, $isAlreadyInStaging, $isNew = false): bool
    {
        $retainStaging = $this->documentRetainsStagingWorkflow($model, $isAlreadyInStaging, $formNameOrMenuDtlId);

        if ($retainStaging && !$isNew) {
            $criteria = $this->getFlowCriteriaForForm($formNameOrMenuDtlId, $formId, true, $model);
            return $criteria !== null && $this->criteriaFlowsForDocument($criteria, $model, $formNameOrMenuDtlId)->isNotEmpty();
        }

        if (!$this->hasStaging($formNameOrMenuDtlId, $formId)) {
            return false;
        }
        if ($isNew) {
            return true;
        }
        if ($this->isDocumentStagingExempt($model, $formNameOrMenuDtlId)) {
            return false;
        }
        return $this->isDocumentStagingEnrolled($model, $formNameOrMenuDtlId);
    }

    public function getFlowCriteriaId($formNameOrMenuDtlId, $formId = null, $skipConditionCheck = false, $model = null)
    {
        $criteria = $this->getFlowCriteriaForForm($formNameOrMenuDtlId, $formId, $skipConditionCheck, $model);
        return $criteria ? $criteria->menu_flow_criteria_id : null;
    }

    protected function scopeAccoVoucherForConditionCheck($formNameOrMenuDtlId, $tableName, $query)
    {
        $t = strtolower((string) $tableName);
        if ($t === '' || strpos($t, 'acco_voucher') === false) {
            return;
        }

        if (strpos($t, 'tbl_acco_voucher') !== false) {
            if (is_numeric($formNameOrMenuDtlId)) {
                $voucherType = config('staging.voucher_type_by_menu.' . $formNameOrMenuDtlId);
                if ($voucherType !== null && $voucherType !== '') {
                    $query->where('voucher_type', $voucherType);
                }
            }
            return;
        }
    }

    protected function scopeAccoVoucherMasterRow($tableName, $query)
    {
        $t = strtolower((string) $tableName);
        if ($t === '' || strpos($t, 'acco_voucher') === false) {
            return;
        }
        try {
            $cols = DB::getSchemaBuilder()->getColumnListing($tableName);
            $hasSrNo = false;
            foreach ($cols as $c) {
                if (strtolower((string) $c) === 'voucher_sr_no') {
                    $hasSrNo = true;
                    break;
                }
            }
            if (!$hasSrNo) {
                return;
            }
            $query->where(function ($q) {
                $q->where('voucher_sr_no', '1')->orWhere('voucher_sr_no', 1);
            });
        } catch (\Throwable $e) {
            return;
        }
    }

    protected function scopeAccoVoucherByMenu($formNameOrMenuDtlId, $tableName, $query)
    {
        $t = strtolower((string) $tableName);
        if ($t === '' || strpos($t, 'acco_voucher') === false) {
            return;
        }
        $this->scopeAccoVoucherMasterRow($tableName, $query);
        if (!is_numeric($formNameOrMenuDtlId)) {
            return;
        }
        $voucherType = config('staging.voucher_type_by_menu.' . $formNameOrMenuDtlId);
        if ($voucherType !== null && $voucherType !== '') {
            $query->where('voucher_type', $voucherType);
        }
    }

    public function tableHasStagingWorkflowColumns($tableName): bool
    {
        static $cache = [];
        $key = strtolower((string) $tableName);
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        try {
            $cols = array_map('strtolower', DB::getSchemaBuilder()->getColumnListing($tableName));
            $cache[$key] = in_array('staging_apply', $cols, true)
                && in_array('current_stg_id', $cols, true)
                && in_array('posted', $cols, true);
        } catch (\Throwable $e) {
            $cache[$key] = false;
        }

        return $cache[$key];
    }

    public function getDocumentsAtFlowStage($formNameOrMenuDtlId, $flowId, $tableName, $primaryKey)
    {
        if (empty($tableName) || $flowId === null || $flowId === '') {
            return collect([]);
        }

        if (!$this->tableHasStagingWorkflowColumns($tableName)) {
            return collect([]);
        }

        $documents = DB::table($tableName)
            ->where('current_stg_id', $flowId)
            ->where('posted', 0)
            ->where('staging_apply', $this->getStagingApplyEnrolledValue($formNameOrMenuDtlId));

        $this->scopeAccoVoucherByMenu($formNameOrMenuDtlId, $tableName, $documents);

        return $documents->get();
    }

    public function getFlowStageCounts($formNameOrMenuDtlId, $tableName)
    {
        $criteria = $this->getFlowCriteriaForForm($formNameOrMenuDtlId);

        if (!$criteria || !$this->tableHasStagingWorkflowColumns($tableName)) {
            return [];
        }

        $enrolledValue = $this->getStagingApplyEnrolledValue($formNameOrMenuDtlId);
        $counts = [];
        foreach ($this->activeCriteriaFlows($criteria) as $flow) {
            $count = DB::table($tableName)
                ->where('current_stg_id', $flow->stg_flows_id)
                ->where('posted', 0)
                ->where('staging_apply', $enrolledValue);

            $this->scopeAccoVoucherByMenu($formNameOrMenuDtlId, $tableName, $count);

            $counts[$flow->stg_flows_id] = $count->count();
        }

        return $counts;
    }
}
