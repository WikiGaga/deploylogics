<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeCurrentStgIdToNumberInPurchaseOrder extends Migration
{
    public function up()
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'oracle') {
            DB::statement('ALTER TABLE tbl_purc_purchase_order MODIFY current_stg_id NUMBER(10) NULL');
        } else {
            Schema::table('tbl_purc_purchase_order', function (Blueprint $table) {
                $table->unsignedInteger('current_stg_id')->nullable()->change();
            });
        }
    }

    public function down()
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'oracle') {
            DB::statement('ALTER TABLE tbl_purc_purchase_order MODIFY current_stg_id VARCHAR2(50) NULL');
        } else {
            Schema::table('tbl_purc_purchase_order', function (Blueprint $table) {
                $table->string('current_stg_id', 50)->nullable()->change();
            });
        }
    }
}
