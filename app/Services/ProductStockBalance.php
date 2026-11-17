<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ProductStockBalance
{
    /** Use the Stock Adjustment balance rules for one product across all packings. */
    public function baseQuantity($productId, $businessId, $companyId, $branchId, $storeId, $date, $excludeDocumentId = null): float
    {
        $ledger = DB::table('VW_PURC_STOCK_DTL')
            ->where('PRODUCT_ID', $productId)
            ->where('BUSINESS_ID', $businessId)
            ->where('COMPANY_ID', $companyId)
            ->where('BRANCH_ID', $branchId)
            ->where('SALES_STORE_ID', $storeId)
            ->where('POSTED', 1)
            ->whereRaw("DOCUMENT_DATE < TO_DATE(?, 'YYYY-MM-DD') + 1", [$date]);
        if ($excludeDocumentId !== null) {
            $ledger->where('DOCUMENT_ID', '<>', $excludeDocumentId);
        }
        $ledgerQuantity = $ledger->sum('QTY_BASE_UNIT_VALUE');

        $productionConsumption = DB::table('TBLPRODUCTIONCONSUMPTION as PC')
            ->join('TBL_PURC_PRODUCT_BARCODE as PB', 'PB.PRODUCT_BARCODE_BARCODE', '=', 'PC.ITEM_CODE')
            ->where('PB.PRODUCT_ID', $productId)
            ->where('PC.BRANCH_ID', $branchId)
            ->where('PC.BUSINESS_ID', $businessId)
            ->where('PC.COMPANY_ID', $companyId)
            ->where('PC.POSTED', 1)
            ->where('PC.STATUS', 1)
            ->where('PC.CANCEL', 0)
            ->whereRaw("PC.RECORD_DATE < TO_DATE(?, 'YYYY-MM-DD') + 1", [$date])
            ->where(function ($query) use ($storeId) {
                $query->where(function ($query) use ($storeId) {
                    $query->whereRaw("LOWER(PC.STOCK_TYPE) = 'production'")
                        ->where('PC.TRANSFER_FROM', $storeId);
                })->orWhere(function ($query) use ($storeId) {
                    $query->whereRaw("LOWER(PC.STOCK_TYPE) = 'consumption'")
                        ->where('PC.TRANSFER_TO', $storeId);
                });
            })
            ->selectRaw("NVL(SUM(CASE WHEN LOWER(PC.STOCK_TYPE) = 'production'
                THEN NVL(PC.QTY, 0) * NVL(PB.PRODUCT_BARCODE_PACKING, 1)
                ELSE -NVL(PC.QTY, 0) * NVL(PB.PRODUCT_BARCODE_PACKING, 1) END), 0) AS QTY")
            ->first()->qty ?? 0;

        // Recipe usage is branch-level data; the source has no store column.
        $recipeUsage = DB::table('ORDER_RECIPE_USAGES')
            ->where('PRODUCT_ID', $productId)
            ->where('RESTAURANT_ID', $branchId)
            ->whereRaw("USAGE_DATE < TO_DATE(?, 'YYYY-MM-DD') + 1", [$date])
            ->sum('PRODUCT_QUANTITY_USED');

        return (float) $ledgerQuantity + (float) $productionConsumption - (float) $recipeUsage;
    }
}
