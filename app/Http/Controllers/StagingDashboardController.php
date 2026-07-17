<?php

namespace App\Http\Controllers;

use App\Services\StagingService;
use App\Library\Utilities;
use App\Models\TblSoftBranch;
use App\Models\TblSoftMenu;
use App\Models\TblSoftMenuDtl;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StagingDashboardController extends Controller
{
    protected $stagingService;

    public function __construct()
    {
        $this->stagingService = new StagingService();
    }

    /**
     * Display staging dashboard with all forms that have staging enabled
     */
    public function index()
    {
        $menus = $this->stagingService->getMenusForStagingDashboard();
        $flowsMenuDtlByMenu = [];

        foreach ($menus as $menuDtl) {
            if (!$this->stagingService->hasStagingDashboardForMenu($menuDtl->menu_dtl_id)) {
                continue;
            }

            $tableName = $menuDtl->menu_dtl_table_name;
            if (empty($tableName)) {
                continue;
            }

            $flows = $this->stagingService->getFormFlowsForDashboard($menuDtl->menu_dtl_id);
            if (empty($flows['all'])) {
                continue;
            }

            $flowCountsByStage = $this->getFlowStageCountsForDashboard($menuDtl->menu_dtl_id, $tableName);
            $menuId = $menuDtl->menu_id;
            if (!isset($flowsMenuDtlByMenu[$menuId])) {
                $flowsMenuDtlByMenu[$menuId] = [
                    'data' => [],
                    'document_count' => []
                ];
            }

            foreach ($flows['all'] as $flow) {
                if (!$this->stagingService->getUserAccessForDashboard($menuDtl->menu_dtl_id, $flow->stg_flows_id)) {
                    continue;
                }

                $flowKey = $flow->stg_flows_id;
                $count = $this->resolveFlowStageCount($flowCountsByStage, $flowKey);

                if (!isset($flowsMenuDtlByMenu[$menuId]['data'][$flowKey])) {
                    $flowsMenuDtlByMenu[$menuId]['data'][$flowKey] = [
                        'flow' => ['stg_flows_name' => $flow->stg_flows_name],
                        'rows' => []
                    ];
                }

                $flowsMenuDtlByMenu[$menuId]['data'][$flowKey]['rows'][$menuDtl->menu_dtl_id] = [
                    'name' => $menuDtl->menu_dtl_name,
                    'count' => $count,
                ];

                if (!isset($flowsMenuDtlByMenu[$menuId]['document_count'][$flowKey])) {
                    $flowsMenuDtlByMenu[$menuId]['document_count'][$flowKey] = 0;
                }
                $flowsMenuDtlByMenu[$menuId]['document_count'][$flowKey] += $count;
            }
        }

        foreach ($flowsMenuDtlByMenu as $menuId => &$item) {
            $item['data'] = array_values($item['data']);
        }
        unset($item);

        $menuIds = array_keys($flowsMenuDtlByMenu);
        $data['menu'] = TblSoftMenu::whereIn('menu_id', $menuIds)
            ->orderBy('menu_sorting')
            ->get();

        // dd($flowsMenuDtlByMenu);
        $data['flows_menu_dtl'] = $flowsMenuDtlByMenu;

        return view('staging_activity.dashboard', compact('data'));
    }

    protected function getFlowStageCountsForDashboard($menuDtlId, $tableName): array
    {
        if (!$this->stagingService->tableHasStagingWorkflowColumns($tableName)) {
            return [];
        }

        try {
            $query = DB::table($tableName)
                ->select('current_stg_id', DB::raw('count(*) as document_count'))
                ->where('posted', 0)
                ->where(
                    'staging_apply',
                    $this->stagingService->getStagingApplyEnrolledValue($menuDtlId)
                )
                ->whereNotNull('current_stg_id');

            $this->applyDashboardDocumentQueryScope($menuDtlId, $tableName, $query);
            $this->stagingService->scopeStockDocumentByMenu($menuDtlId, $tableName, $query);
            $this->stagingService->scopeAccessibleBranches($tableName, $query);

            $counts = [];
            foreach ($query->groupBy('current_stg_id')->get() as $row) {
                $row = (array) $row;
                $stageId = $row['current_stg_id']
                    ?? $row['CURRENT_STG_ID']
                    ?? null;
                if ($stageId === null || $stageId === '') {
                    continue;
                }
                $counts[(string) $stageId] = (int) (
                    $row['document_count']
                    ?? $row['DOCUMENT_COUNT']
                    ?? 0
                );
            }

            return $counts;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function resolveFlowStageCount(array $flowCountsByStage, $flowId): int
    {
        $key = (string) $flowId;

        return (int) ($flowCountsByStage[$key] ?? 0);
    }

    protected function applyDashboardDocumentQueryScope($menuDtlId, $tableName, $query): void
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
            if ($hasSrNo) {
                $query->where(function ($q) {
                    $q->where('voucher_sr_no', '1')->orWhere('voucher_sr_no', 1);
                });
            }
        } catch (\Throwable $e) {
            return;
        }

        if (!is_numeric($menuDtlId)) {
            return;
        }

        $voucherType = config('staging.voucher_type_by_menu.' . $menuDtlId);
        if ($voucherType !== null && $voucherType !== '') {
            $query->where('voucher_type', $voucherType);
        }
    }

    protected function getDocumentCountAtStage($menuDtlId, $tableName, $flowId): int
    {
        if (!$this->stagingService->tableHasStagingWorkflowColumns($tableName)) {
            return 0;
        }

        try {
            $query = DB::table($tableName)
                ->where('current_stg_id', $flowId)
                ->where('posted', 0)
                ->where(
                    'staging_apply',
                    $this->stagingService->getStagingApplyEnrolledValue($menuDtlId)
                );

            $this->applyDashboardDocumentQueryScope($menuDtlId, $tableName, $query);
            $this->stagingService->scopeStockDocumentByMenu($menuDtlId, $tableName, $query);
            $this->stagingService->scopeAccessibleBranches($tableName, $query);

            return (int) $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getFormConfig($menuDtlId, $tableName)
    {
        $configs = [
            38 => [
                'path' => '/purchase-order/form/',
                'pk' => 'purchase_order_id',
                'cols' => ['purchase_order_code', 'purchase_order_entry_date'],
                'titles' => ['PO NO', 'Date'],
            ],
            23 => [
                'path' => '/grn/form/',
                'pk' => 'grn_id',
                'cols' => ['grn_code', 'grn_date'],
                'titles' => ['GRN NO', 'Date'],
            ],
            354 => [
                'path' => '/shift_sessions/form/',
                'pk' => 'session_id',
                'cols' => ['session_no', 'session_id'],
                'titles' => ['Session no', 'ID'],
            ],
            54 => [
                'path' => '/stock/opening-stock/form/',
                'pk' => 'stock_id',
                'cols' => ['stock_code', 'stock_date'],
                'titles' => ['Stock Code', 'Date'],
            ],
            65 => [
                'path' => '/stock/stock-transfer/form/',
                'pk' => 'stock_id',
                'cols' => ['stock_code', 'stock_date'],
                'titles' => ['Stock Code', 'Date'],
            ],
            76 => [
                'path' => '/stock/stock-receiving/form/',
                'pk' => 'stock_id',
                'cols' => ['stock_code', 'stock_date'],
                'titles' => ['Stock Code', 'Date'],
            ],
            31 => [
                'path' => '/accounts/jv/form/',
                'pk' => 'voucher_id',
                'cols' => ['voucher_no', 'voucher_date'],
                'titles' => ['Voucher No', 'Voucher Date'],
            ],
            28 => [
                'path' => '/accounts/crv/form/',
                'pk' => 'voucher_id',
                'cols' => ['voucher_no', 'voucher_date'],
                'titles' => ['Voucher No', 'Voucher Date'],
            ],
            37 => [
                'path' => '/accounts/cpv/form/',
                'pk' => 'voucher_id',
                'cols' => ['voucher_no', 'voucher_date'],
                'titles' => ['Voucher No', 'Voucher Date'],
            ],
            29 => [
                'path' => '/accounts/brv/form/',
                'pk' => 'voucher_id',
                'cols' => ['voucher_no', 'voucher_date'],
                'titles' => ['Voucher No', 'Voucher Date'],
            ],
            36 => [
                'path' => '/accounts/bpv/form/',
                'pk' => 'voucher_id',
                'cols' => ['voucher_no', 'voucher_date'],
                'titles' => ['Voucher No', 'Voucher Date'],
            ],
            62 => [
                'path' => '/accounts/obv/form/',
                'pk' => 'voucher_id',
                'cols' => ['voucher_no', 'voucher_date'],
                'titles' => ['Voucher No', 'Voucher Date'],
            ],
        ];

        if (isset($configs[$menuDtlId])) {
            return $configs[$menuDtlId];
        }

        $base = preg_replace('/^tbl_[a-z]+_/', '', $tableName);
        $pk = $base . '_id';
        return [
            'path' => '/',
            'pk' => $pk,
            'cols' => [$pk],
            'titles' => ['ID'],
        ];
    }

    protected function formatStagingDashboardCellValue(string $column, $value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        $str = trim((string) $value);
        if (stripos($column, 'date') !== false && preg_match('/^\d{4}-\d{2}-\d{2}/', $str)) {
            return substr($str, 0, 10);
        }

        return $str;
    }

    protected function sortStagingDashboardRowsByDocumentNumber(array $rows, string $sortColumn): array
    {
        usort($rows, function ($a, $b) use ($sortColumn) {
            $left = (string) ($a[$sortColumn] ?? '');
            $right = (string) ($b[$sortColumn] ?? '');

            return strnatcasecmp($right, $left);
        });

        return $rows;
    }

    protected function branchNameForDocument($branchId, Collection $branchNames): string
    {
        if ($branchId === null || $branchId === '') {
            return '—';
        }
        $resolved = $branchNames->get($branchId)
            ?? $branchNames->get((string) $branchId)
            ?? $branchNames->get((int) $branchId);
        if ($resolved !== null) {
            return $resolved;
        }

        return (string) $branchId;
    }

    protected function stagingDashboardDocumentLink(string $path, $documentId, $documentBranchId): string
    {
        if ($documentId === null || $documentId === '') {
            return '#';
        }
        $href = url($path . $documentId);
        if ($documentBranchId === null || $documentBranchId === '' || Utilities::documentBranchIsUserAccessible($documentBranchId)) {
            return $href;
        }
        $sep = strpos($href, '?') !== false ? '&' : '?';

        return $href . $sep . 'view=1';
    }

    /**
     * Show module list for a specific form
     */
    public function moduleList($menuDtlId)
    {
        $menu = TblSoftMenuDtl::find($menuDtlId);

        if (!$menu || !$this->stagingService->menuHasStagingDefined($menuDtlId)) {
            abort(404);
        }

        $config = $this->getFormConfig($menuDtlId, $menu->menu_dtl_table_name);
        $pk = $config['pk'];

        $flows = $this->stagingService->getFormFlowsForDashboard($menuDtlId);
        $branchNames = TblSoftBranch::query()->pluck('branch_name', 'branch_id');
        $flowsMenuDtl = [
            'cols' => array_merge(['_branch_display'], $config['cols']),
            'titles' => array_merge(['Branch'], $config['titles']),
        ];

        foreach ($flows['all'] as $flow) {
            if ($this->stagingService->getUserAccessForDashboard($menuDtlId, $flow->stg_flows_id)) {
                $documents = $this->stagingService->getDocumentsAtFlowStage(
                    $menuDtlId,
                    $flow->stg_flows_id,
                    $menu->menu_dtl_table_name,
                    $pk
                );

                $rows = [];
                foreach ($documents as $doc) {
                    $arr = is_array($doc) ? $doc : (array) $doc;
                    $row = [];
                    $branchId = $arr['branch_id'] ?? $arr['BRANCH_ID'] ?? null;
                    $row['_branch_display'] = $this->branchNameForDocument($branchId, $branchNames);
                    foreach ($config['cols'] as $col) {
                        $raw = $arr[$col] ?? $arr[strtoupper($col)] ?? '';
                        $row[$col] = $this->formatStagingDashboardCellValue($col, $raw);
                    }
                    $id = $arr[$pk] ?? $arr[strtoupper($pk)] ?? null;
                    $row['link'] = $this->stagingDashboardDocumentLink($config['path'], $id, $branchId);
                    $rows[] = $row;
                }

                $rows = $this->sortStagingDashboardRowsByDocumentNumber($rows, $config['cols'][0]);

                $flowsMenuDtl[$flow->stg_flows_id] = $rows;
            }
        }

        $data['menu_dtl'] = $menu;
        $data['flows'] = $flows['all'];
        $data['flows_menu_dtl'] = $flowsMenuDtl;

        return view('staging_activity.stg_form_detail', compact('data'));
    }
}
