<?php

namespace App\Console\Commands;

use App\Models\TblPosVoucherBranchSync;
use App\Models\TblSoftBranch;
use App\Services\PosOrderVoucherService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherOrderSync extends Command
{
    protected $signature = 'voucher:order-sync
                            {--lookback=3 : Days to scan on first run / missing-voucher catch-up}
                            {--overlap=5 : Minutes to overlap previous watermark}
                            {--branch= : Optional branch_id to process only one branch}';

    protected $description = 'Sync POS/RPOS vouchers per branch using order updated_at watermarks';

    public function handle(PosOrderVoucherService $service)
    {
        $lookbackDays = max(1, (int) $this->option('lookback'));
        $overlapMinutes = max(0, (int) $this->option('overlap'));
        $onlyBranch = $this->option('branch');

        $branchesQuery = TblSoftBranch::where('branch_active_status', 1);
        if (!empty($onlyBranch)) {
            $branchesQuery->where('branch_id', $onlyBranch);
        }
        $branches = $branchesQuery->get(['branch_id', 'branch_name']);

        if ($branches->isEmpty()) {
            $this->warn('No active branches found.');
            return 0;
        }

        $totalProcessed = 0;

        foreach ($branches as $branch) {
            $branchId = (int) $branch->branch_id;
            $this->info("Processing branch {$branchId} ({$branch->branch_name})");

            try {
                $processed = $this->processBranch($branchId, $lookbackDays, $overlapMinutes, $service);
                $totalProcessed += $processed;
                $this->info("Branch {$branchId}: processed {$processed} order(s)");
            } catch (Exception $e) {
                Log::error('voucher:order-sync branch failed', [
                    'branch_id' => $branchId,
                    'message' => $e->getMessage(),
                ]);
                $this->error("Branch {$branchId} failed: " . $e->getMessage());
            }
        }

        $this->info("Done. Total processed: {$totalProcessed}");
        return 0;
    }

    protected function processBranch($branchId, $lookbackDays, $overlapMinutes, PosOrderVoucherService $service)
    {
        $sync = TblPosVoucherBranchSync::firstOrCreate(
            ['branch_id' => $branchId],
            [
                'last_order_updated_at' => null,
                'last_run_at' => null,
                'last_processed_count' => 0,
            ]
        );

        if ($sync->last_order_updated_at) {
            $from = Carbon::parse($sync->last_order_updated_at)->subMinutes($overlapMinutes);
        } else {
            $from = Carbon::now()->subDays($lookbackDays);
        }

        $changedOrders = DB::table('orders')
            ->select('id', 'updated_at')
            ->where('restaurant_id', $branchId)
            ->where('updated_at', '>', $from)
            ->orderBy('updated_at')
            ->get();

        $missingOrderIds = $this->getMissingVoucherOrderIds($branchId, $lookbackDays);

        $orderMap = [];
        foreach ($changedOrders as $row) {
            $orderMap[(string) $row->id] = $row->updated_at;
        }
        foreach ($missingOrderIds as $orderId) {
            $key = (string) $orderId;
            if (!isset($orderMap[$key])) {
                $orderMap[$key] = null;
            }
        }

        $processedCount = 0;
        $maxChangedUpdatedAt = null;

        foreach ($orderMap as $orderId => $updatedAt) {
            try {
                DB::beginTransaction();
                $result = $service->syncOrder($orderId);
                DB::commit();

                $processedCount++;

                if ($updatedAt) {
                    $ts = Carbon::parse($updatedAt);
                    if (!$maxChangedUpdatedAt || $ts->gt($maxChangedUpdatedAt)) {
                        $maxChangedUpdatedAt = $ts;
                    }
                }

                if (($result['action'] ?? '') === 'skipped') {
                    $this->line("  order {$orderId}: skipped - " . ($result['reason'] ?? ''));
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('voucher:order-sync order failed', [
                    'branch_id' => $branchId,
                    'order_id' => $orderId,
                    'message' => $e->getMessage(),
                ]);
                $this->error("  order {$orderId} failed: " . $e->getMessage());
            }
        }

        $sync->last_run_at = Carbon::now();
        $sync->last_processed_count = $processedCount;

        if ($maxChangedUpdatedAt) {
            $current = $sync->last_order_updated_at
                ? Carbon::parse($sync->last_order_updated_at)
                : null;

            if (!$current || $maxChangedUpdatedAt->gt($current)) {
                $sync->last_order_updated_at = $maxChangedUpdatedAt;
            }
        }

        $sync->save();

        return $processedCount;
    }

    protected function getMissingVoucherOrderIds($branchId, $lookbackDays)
    {
        $fromDate = Carbon::now()->subDays($lookbackDays)->format('Y-m-d');

        $sql = "
            SELECT DISTINCT v.order_id
            FROM VW_REST_SUMMARY_ORDER_WISE v
            WHERE v.branch_id = ?
              AND v.order_date >= to_date(?, 'yyyy-mm-dd')
              AND (
                    (
                        UPPER(TRIM(v.sales_type)) = 'POS'
                        AND LOWER(TRIM(v.payment_status)) = 'paid'
                        AND LOWER(TRIM(v.order_status)) <> 'canceled'
                    )
                    OR UPPER(TRIM(v.sales_type)) = 'RPOS'
              )
              AND NOT EXISTS (
                    SELECT 1
                    FROM tbl_acco_voucher a
                    WHERE TO_CHAR(a.voucher_document_id) = TO_CHAR(v.order_id)
                      AND a.voucher_type IN ('POS', 'RPOS')
              )
        ";

        $rows = DB::select($sql, [$branchId, $fromDate]);

        return array_map(function ($row) {
            return $row->order_id;
        }, $rows);
    }
}
