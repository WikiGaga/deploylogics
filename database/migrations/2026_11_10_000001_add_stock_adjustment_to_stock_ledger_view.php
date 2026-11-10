<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddStockAdjustmentToStockLedgerView extends Migration
{
    private const MARKER = '/* STOCK_ADJUSTMENT_LEDGER */';

    public function up()
    {
        $ddl = $this->currentViewDdl();
        if (strpos($ddl, self::MARKER) !== false) {
            return;
        }

        DB::unprepared(rtrim($ddl, " \t\n\r\0\x0B;") . $this->stockAdjustmentSql());

        // Keep historical adjustments consistent with the selected barcode packing.
        DB::statement("
            UPDATE TBL_INVE_STOCK_DTL D
               SET D.STOCK_DTL_QTY_BASE_UNIT =
                   NVL(D.STOCK_DTL_QUANTITY, 0)
                   * NVL(NULLIF(D.STOCK_DTL_PACKING, 0), 1)
             WHERE EXISTS (
                   SELECT 1
                     FROM TBL_INVE_STOCK M
                    WHERE M.STOCK_ID = D.STOCK_ID
                      AND UPPER(M.STOCK_CODE_TYPE) = 'SA'
             )
        ");
    }

    public function down()
    {
        $ddl = $this->currentViewDdl();
        $position = strpos($ddl, self::MARKER);
        if ($position === false) {
            return;
        }

        $unionPosition = strrpos(substr($ddl, 0, $position), 'UNION ALL');
        if ($unionPosition === false) {
            throw new RuntimeException('Unable to locate the Stock Adjustment view extension.');
        }

        DB::unprepared(rtrim(substr($ddl, 0, $unionPosition), " \t\n\r\0\x0B;") . PHP_EOL);
    }

    private function currentViewDdl(): string
    {
        $row = DB::selectOne("SELECT DBMS_METADATA.GET_DDL('VIEW', 'VW_PURC_STOCK_DTL') AS DDL FROM DUAL");
        $ddl = $row->ddl ?? $row->DDL ?? null;

        if (is_resource($ddl)) {
            $ddl = stream_get_contents($ddl);
        }

        if (!is_string($ddl) || trim($ddl) === '') {
            throw new RuntimeException('Unable to read VW_PURC_STOCK_DTL definition.');
        }

        return $ddl;
    }

    private function stockAdjustmentSql(): string
    {
        return <<<'SQL'

    UNION ALL
    /* STOCK_ADJUSTMENT_LEDGER */
    SELECT M.BUSINESS_ID,
           M.COMPANY_ID,
           M.BRANCH_ID,
           M.STOCK_STORE_FROM_ID SALES_STORE_ID,
           '' SALES_STORE_NAME,
           M.BRANCH_ID TRANSFER_FROM_BRANCH_ID,
           NULL TRANSFER_TO_BRANCH_ID,
           TO_CHAR(D.STOCK_DTL_BATCH_NO) BATCH_NO,
           TO_CHAR(M.STOCK_LOCATION_ID) SHELF,
           TO_DATE(D.STOCK_DTL_EXPIRY_DATE) STOCK_EXPIRY,
           M.STOCK_DATE DOCUMENT_DATE,
           M.STOCK_ID DOCUMENT_ID,
           M.STOCK_CODE DOCUMENT_CODE,
           UPPER(M.STOCK_CODE_TYPE) DOCUMENT_TYPE,
           CASE WHEN NVL(D.STOCK_DTL_QUANTITY, 0) >= 0 THEN '+' ELSE '-' END STOCK_CALCULATION_EFFECT,
           D.PRODUCT_ID,
           D.PRODUCT_BARCODE_ID,
           D.PRODUCT_BARCODE_BARCODE SALES_DTL_BARCODE,
           D.UOM_ID,
           NVL(NULLIF(D.STOCK_DTL_PACKING, 0), 1) PRODUCT_PACKING,
           ABS(NVL(D.STOCK_DTL_QTY_BASE_UNIT, 0)) QTY_BASE_UNIT,
           ABS(NVL(D.STOCK_DTL_QUANTITY, 0)) QTY_PER_UNIT,
           CASE WHEN NVL(D.STOCK_DTL_QUANTITY, 0) > 0
                THEN D.STOCK_DTL_QUANTITY ELSE 0 END QTY_IN,
           0 BONUS_QTY_IN,
           CASE WHEN NVL(D.STOCK_DTL_QUANTITY, 0) < 0
                THEN ABS(D.STOCK_DTL_QUANTITY) ELSE 0 END QTY_OUT,
           0 BONUS_QTY_OUT,
           NVL(D.STOCK_DTL_RATE, 0) STOCK_RATE,
           0 STOCK_RATE_EXPENSE,
           NVL(D.STOCK_DTL_AMOUNT,
               NVL(D.STOCK_DTL_RATE, 0) * NVL(D.STOCK_DTL_QUANTITY, 0)) STOCK_VALUE,
           4 SORTING_ID,
           NVL(D.STOCK_DTL_QUANTITY, 0) QTY_VALUE,
           NVL(D.STOCK_DTL_QTY_BASE_UNIT, 0) QTY_BASE_UNIT_VALUE,
           CASE WHEN NVL(D.STOCK_DTL_QUANTITY, 0) > 0 THEN 1 ELSE 0 END RATE_EFFECT,
           NVL(D.STOCK_DTL_RATE, 0) DOCUMENT_RATE,
           NVL(D.STOCK_DTL_RATE, 0) DOCUMENT_ACT_RATE,
           NVL(D.STOCK_DTL_RATE, 0) COST_RATE,
           D.COST_POSTING,
           NVL(M.POSTED, 0) POSTED,
           M.CREATED_AT,
           M.SUPPLIER_ID
      FROM TBL_INVE_STOCK M
      JOIN TBL_INVE_STOCK_DTL D ON D.STOCK_ID = M.STOCK_ID
     WHERE UPPER(M.STOCK_CODE_TYPE) = 'SA'
SQL;
    }
}
