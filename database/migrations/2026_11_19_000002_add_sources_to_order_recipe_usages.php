<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSourcesToOrderRecipeUsages extends Migration
{
    public function up()
    {
        $columns = DB::select("SELECT COLUMN_NAME, NULLABLE FROM USER_TAB_COLUMNS WHERE TABLE_NAME = 'ORDER_RECIPE_USAGES' AND COLUMN_NAME IN ('OPTION_LIST_ID', 'FOOD_RECIPE_ID', 'SOURCE_BREAKDOWN')");
        $byName = [];
        foreach ($columns as $column) {
            $byName[$column->column_name ?? $column->COLUMN_NAME] = $column;
        }
        if (!isset($byName['SOURCE_BREAKDOWN'])) {
            DB::statement('ALTER TABLE ORDER_RECIPE_USAGES ADD (SOURCE_BREAKDOWN CLOB)');
        }
        foreach (['OPTION_LIST_ID', 'FOOD_RECIPE_ID'] as $name) {
            $column = $byName[$name] ?? null;
            if (!$column) throw new RuntimeException($name . ' is missing from ORDER_RECIPE_USAGES.');
            if (($column->nullable ?? $column->NULLABLE) === 'N') {
                DB::statement('ALTER TABLE ORDER_RECIPE_USAGES MODIFY (' . $name . ' NULL)');
            }
        }
    }

    public function down()
    {
        if (DB::table('ORDER_RECIPE_USAGES')->whereNotNull('source_breakdown')->exists()) {
            throw new RuntimeException('Cannot remove source breakdown while synced usage rows depend on it.');
        }
        if (DB::table('ORDER_RECIPE_USAGES')->whereNull('option_list_id')->orWhereNull('food_recipe_id')->exists()) {
            throw new RuntimeException('Cannot restore source constraints while usage rows have null source IDs.');
        }
        DB::statement('ALTER TABLE ORDER_RECIPE_USAGES MODIFY (OPTION_LIST_ID NOT NULL, FOOD_RECIPE_ID NOT NULL)');
        DB::statement('ALTER TABLE ORDER_RECIPE_USAGES DROP COLUMN SOURCE_BREAKDOWN');
    }
}
