<?php

namespace App\Http\Controllers;

use App\Services\StagingService;
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
        $menus = TblSoftMenuDtl::all();
        $flowsMenuDtlByMenu = [];

        foreach ($menus as $menuDtl) {
            if (!$this->stagingService->hasStaging($menuDtl->menu_dtl_id)) {
                continue;
            }

            $flows = $this->stagingService->getFormFlows($menuDtl->menu_dtl_id, null, null);
            if (empty($flows['all']) || empty($menuDtl->menu_dtl_table_name)) {
                continue;
            }

            $menuId = $menuDtl->menu_id;
            if (!isset($flowsMenuDtlByMenu[$menuId])) {
                $flowsMenuDtlByMenu[$menuId] = [
                    'data' => [],
                    'document_count' => []
                ];
            }

            foreach ($flows['all'] as $flow) {
                if (!$this->stagingService->getUserAccess($menuDtl->menu_dtl_id, $flow->stg_flows_id)) {
                    continue;
                }

                $documents = $this->stagingService->getDocumentsAtFlowStage(
                    $menuDtl->menu_dtl_id,
                    $flow->stg_flows_id,
                    $menuDtl->menu_dtl_table_name,
                    $menuDtl->menu_dtl_table_name . '_id'
                );

                $flowKey = $flow->stg_flows_id;
                if (!isset($flowsMenuDtlByMenu[$menuId]['data'][$flowKey])) {
                    $flowsMenuDtlByMenu[$menuId]['data'][$flowKey] = [
                        'flow' => ['stg_flows_name' => $flow->stg_flows_name],
                        'rows' => []
                    ];
                }

                $flowsMenuDtlByMenu[$menuId]['data'][$flowKey]['rows'][$menuDtl->menu_dtl_id] = [
                    'name' => $menuDtl->menu_dtl_name,
                    'data' => $documents->toArray()
                ];

                $count = count($documents);
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

        $data['flows_menu_dtl'] = $flowsMenuDtlByMenu;

        return view('staging_activity.dashboard', compact('data'));
    }

    /**
     * Get count of documents at a specific stage
     */
    protected function getDocumentCountAtStage($tableName, $flowId)
    {
        try {
            $query = DB::table($tableName)
                ->where('current_stg_id', $flowId)
                ->where('posted', 0)
                ->where('staging_apply', 0);

            return $query->count();
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
            354 => [
                'path' => '/shift_sessions/form/',
                'pk' => 'session_id',
                'cols' => ['session_no', 'session_id'],
                'titles' => ['Session no', 'ID'],
            ],
            54 => [
                'path' => '/stock/opening-stock/form/',
                'pk' => 'stock_id',
                'cols' => ['stock_code', 'stock_id'],
                'titles' => ['Stock Code', 'ID'],
            ],
            31 => [
                'path' => '/accounts/jv/form/',
                'pk' => 'voucher_id',
                'cols' => ['voucher_no', 'voucher_date'],
                'titles' => ['Voucher No', 'Voucher Date'],
            ],
            65 => [
                'path' => '/stock/stock-transfer/form/',
                'pk' => 'stock_id',
                'cols' => ['stock_code', 'stock_id'],
                'titles' => ['Stock Code', 'ID'],
            ],
            76 => [
                'path' => '/stock/stock-receiving/form/',
                'pk' => 'stock_id',
                'cols' => ['stock_code', 'stock_id'],
                'titles' => ['Stock Code', 'ID'],
            ],
            28 => [
                'path' => '/accounts/crv/form/',
                'pk' => 'voucher_id',
                'cols' => ['voucher_code', 'voucher_id'],
                'titles' => ['Voucher Code', 'ID'],
            ],
            37 => [
                'path' => '/accounts/cpv/form/',
                'pk' => 'voucher_id',
                'cols' => ['voucher_code', 'voucher_id'],
                'titles' => ['Voucher Code', 'ID'],
            ],
            29 => [
                'path' => '/accounts/brv/form/',
                'pk' => 'voucher_id',
                'cols' => ['voucher_code', 'voucher_id'],
                'titles' => ['Voucher Code', 'ID'],
            ],
            36 => [
                'path' => '/accounts/bpv/form/',
                'pk' => 'voucher_id',
                'cols' => ['voucher_code', 'voucher_id'],
                'titles' => ['Voucher Code', 'ID'],
            ],
            62 => [
                'path' => '/accounts/obv/form/',
                'pk' => 'voucher_id',
                'cols' => ['voucher_code', 'voucher_id'],
                'titles' => ['Voucher Code', 'ID'],
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

    /**
     * Show module list for a specific form
     */
    public function moduleList($menuDtlId)
    {
        $menu = TblSoftMenuDtl::find($menuDtlId);

        if (!$menu || !$this->stagingService->hasStaging($menuDtlId)) {
            abort(404);
        }

        $config = $this->getFormConfig($menuDtlId, $menu->menu_dtl_table_name);
        $pk = $config['pk'];

        $flows = $this->stagingService->getFormFlows($menuDtlId, null, null);
        $branchNames = TblSoftBranch::query()->pluck('branch_name', 'branch_id');
        $flowsMenuDtl = [
            'cols' => array_merge(['_branch_display'], $config['cols']),
            'titles' => array_merge(['Branch'], $config['titles']),
        ];

        foreach ($flows['all'] as $flow) {
            if ($this->stagingService->getUserAccess($menuDtlId, $flow->stg_flows_id)) {
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
                        $row[$col] = $arr[$col] ?? $arr[strtoupper($col)] ?? '';
                    }
                    $id = $arr[$pk] ?? $arr[strtoupper($pk)] ?? null;
                    $row['link'] = $id ? url($config['path'] . $id) : '#';
                    $rows[] = $row;
                }

                $flowsMenuDtl[$flow->stg_flows_id] = $rows;
            }
        }

        $data['menu_dtl'] = $menu;
        $data['flows'] = $flows['all'];
        $data['flows_menu_dtl'] = $flowsMenuDtl;

        return view('staging_activity.stg_form_detail', compact('data'));
    }
}
