<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStagingColumnsToPurchaseOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_purc_purchase_order', function (Blueprint $table) {
            // Add staging columns if they don't exist
            if (!Schema::hasColumn('tbl_purc_purchase_order', 'current_stg_id')) {
                $table->string('current_stg_id', 50)->nullable()->after('purchase_order_user_id');
            }
            if (!Schema::hasColumn('tbl_purc_purchase_order', 'staging_apply')) {
                $table->tinyInteger('staging_apply')->default(0)->after('current_stg_id');
            }
            if (!Schema::hasColumn('tbl_purc_purchase_order', 'posted')) {
                $table->tinyInteger('posted')->default(0)->after('staging_apply');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_purc_purchase_order', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_purc_purchase_order', 'current_stg_id')) {
                $table->dropColumn('current_stg_id');
            }
            if (Schema::hasColumn('tbl_purc_purchase_order', 'staging_apply')) {
                $table->dropColumn('staging_apply');
            }
            if (Schema::hasColumn('tbl_purc_purchase_order', 'posted')) {
                $table->dropColumn('posted');
            }
        });
    }
}
