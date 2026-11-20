<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRecipeTypeToFoodRecipes extends Migration
{
    public function up()
    {
        $column = DB::selectOne("SELECT COUNT(*) AS CNT FROM USER_TAB_COLUMNS WHERE TABLE_NAME = 'FOOD_RECIPES' AND COLUMN_NAME = 'RECIPE_TYPE'");
        if ((int) ($column->cnt ?? $column->CNT ?? 0) === 0) {
            DB::statement("ALTER TABLE FOOD_RECIPES ADD (RECIPE_TYPE VARCHAR2(20 CHAR) DEFAULT 'option' NOT NULL)");
        }
    }

    public function down()
    {
        if (DB::table('FOOD_RECIPES')->where('recipe_type', 'addon')->exists()) {
            throw new RuntimeException('Cannot remove recipe type while add-on recipes exist.');
        }
        DB::statement('ALTER TABLE FOOD_RECIPES DROP COLUMN RECIPE_TYPE');
    }
}
