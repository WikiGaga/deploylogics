<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RequireReceivingForNewStockDistributions extends Migration
{
    public function up()
    {
        $column = DB::selectOne("SELECT COUNT(*) AS CNT FROM USER_TAB_COLUMNS
            WHERE TABLE_NAME = 'TBL_INVE_STOCK' AND COLUMN_NAME = 'STOCK_RECEIVE_REQUIRED'");
        if ((int) ($column->cnt ?? $column->CNT ?? 0) === 0) {
            DB::statement('ALTER TABLE TBL_INVE_STOCK ADD (STOCK_RECEIVE_REQUIRED NUMBER(1) DEFAULT 0 NOT NULL)');
        }
        $ddl = $this->viewDdl();
        if (strpos($ddl, "AND NVL(M.STOCK_RECEIVE_REQUIRED, 0) = 0") !== false) return;
        $this->replaceDestinationCondition(
            "WHERE UPPER(M.STOCK_CODE_TYPE) = 'SD'",
            "WHERE UPPER(M.STOCK_CODE_TYPE) = 'SD' AND NVL(M.STOCK_RECEIVE_REQUIRED, 0) = 0"
        );
    }

    public function down()
    {
        if (DB::table('TBL_INVE_STOCK')->where('stock_receive_required', 1)->exists()) {
            throw new RuntimeException('Cannot roll back while receiving-required Stock Distributions exist.');
        }
        $this->replaceDestinationCondition(
            "WHERE UPPER(M.STOCK_CODE_TYPE) = 'SD' AND NVL(M.STOCK_RECEIVE_REQUIRED, 0) = 0",
            "WHERE UPPER(M.STOCK_CODE_TYPE) = 'SD'"
        );
        DB::statement('ALTER TABLE TBL_INVE_STOCK DROP COLUMN STOCK_RECEIVE_REQUIRED');
    }

    private function replaceDestinationCondition(string $old, string $new): void
    {
        $ddl = $this->viewDdl();
        $marker = '/* STOCK_DISTRIBUTION_DESTINATION */';
        $position = strpos($ddl, $marker);
        if ($position === false) {
            throw new RuntimeException('Stock Distribution destination section was not found in the stock ledger view.');
        }
        $before = substr($ddl, 0, $position);
        $after = substr($ddl, $position);
        $replaced = preg_replace('/' . preg_quote($old, '/') . '/i', $new, $after, 1, $count);
        if ($count !== 1) {
            throw new RuntimeException('Stock Distribution destination condition was not found in the stock ledger view.');
        }
        DB::unprepared(rtrim($before . $replaced, " \t\n\r\0\x0B;"));
    }

    private function viewDdl(): string
    {
        $row = DB::selectOne("SELECT DBMS_METADATA.GET_DDL('VIEW', 'VW_PURC_STOCK_DTL') AS DDL FROM DUAL");
        $ddl = $row->ddl ?? $row->DDL ?? null;
        if (is_resource($ddl)) {
            $ddl = stream_get_contents($ddl);
        }
        if (!is_string($ddl) || trim($ddl) === '') {
            throw new RuntimeException('Unable to read stock ledger view definition.');
        }
        return $ddl;
    }
}
