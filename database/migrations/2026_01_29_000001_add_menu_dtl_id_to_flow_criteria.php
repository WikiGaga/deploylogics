<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMenuDtlIdToFlowCriteria extends Migration
{
    public function up()
    {
        Schema::table('tbl_menu_flow_criteria', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_menu_flow_criteria', 'menu_dtl_id')) {
                $table->string('menu_dtl_id', 50)->nullable()->after('menu_flow_criteria_name');
            }
        });
    }

    public function down()
    {
        Schema::table('tbl_menu_flow_criteria', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_menu_flow_criteria', 'menu_dtl_id')) {
                $table->dropColumn('menu_dtl_id');
            }
        });
    }
}
