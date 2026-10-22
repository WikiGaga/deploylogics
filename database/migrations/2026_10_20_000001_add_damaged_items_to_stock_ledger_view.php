<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDamagedItemsToStockLedgerView extends Migration
{
    private const MARKER = '/* DAMAGED_ITEMS_STOCK_OUT */';

    public function up()
    {
        $ddl = $this->currentViewDdl();
        if (strpos($ddl, self::MARKER) !== false) {
            return;
        }

        DB::unprepared(rtrim($ddl, " \t\n\r\0\x0B;") . $this->damagedItemsSql());

        DB::statement("
            UPDATE TBL_INVE_STOCK_DTL D
               SET D.STOCK_DTL_QTY_BASE_UNIT =
                   NVL(D.STOCK_DTL_QUANTITY, 0) * NVL(NULLIF(D.STOCK_DTL_PACKING, 0), 1)
             WHERE EXISTS (
                   SELECT 1
                     FROM TBL_INVE_STOCK M
                    WHERE M.STOCK_ID = D.STOCK_ID
                      AND UPPER(M.STOCK_CODE_TYPE) = 'DI'
             )
        ");

        DB::statement("
            UPDATE TBL_INVE_STOCK M
               SET M.STOCK_BRANCH_FROM_ID = M.BRANCH_ID
             WHERE UPPER(M.STOCK_CODE_TYPE) = 'DI'
               AND M.STOCK_BRANCH_FROM_ID IS NULL
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
            throw new RuntimeException('Unable to locate the Damaged Items view extension.');
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

    private function damagedItemsSql(): string
    {
        return <<<'SQL'

    UNION ALL
    /* DAMAGED_ITEMS_STOCK_OUT */
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
           '-' STOCK_CALCULATION_EFFECT,
           D.PRODUCT_ID,
           D.PRODUCT_BARCODE_ID,
           D.PRODUCT_BARCODE_BARCODE SALES_DTL_BARCODE,
           D.UOM_ID,
           NVL(D.STOCK_DTL_PACKING, 0) PRODUCT_PACKING,
           D.STOCK_DTL_QTY_BASE_UNIT QTY_BASE_UNIT,
           D.STOCK_DTL_QUANTITY QTY_PER_UNIT,
           0 QTY_IN,
           0 BONUS_QTY_IN,
           NVL(D.STOCK_DTL_QUANTITY, 0) QTY_OUT,
           0 BONUS_QTY_OUT,
           NVL(D.STOCK_DTL_RATE, 0) STOCK_RATE,
           0 STOCK_RATE_EXPENSE,
           NVL(D.STOCK_DTL_AMOUNT, NVL(D.STOCK_DTL_RATE, 0) * NVL(D.STOCK_DTL_QUANTITY, 0)) STOCK_VALUE,
           5 SORTING_ID,
           -NVL(D.STOCK_DTL_QUANTITY, 0) QTY_VALUE,
           -NVL(D.STOCK_DTL_QTY_BASE_UNIT, 0) QTY_BASE_UNIT_VALUE,
           0 RATE_EFFECT,
           NVL(D.STOCK_DTL_RATE, 0) DOCUMENT_RATE,
           NVL(D.STOCK_DTL_RATE, 0) DOCUMENT_ACT_RATE,
           NVL(D.STOCK_DTL_RATE, 0) COST_RATE,
           D.COST_POSTING,
           NVL(M.POSTED, 0) POSTED,
           M.CREATED_AT,
           M.SUPPLIER_ID
      FROM TBL_INVE_STOCK M
      JOIN TBL_INVE_STOCK_DTL D ON D.STOCK_ID = M.STOCK_ID
     WHERE UPPER(M.STOCK_CODE_TYPE) = 'DI'
SQL;
    }
}
