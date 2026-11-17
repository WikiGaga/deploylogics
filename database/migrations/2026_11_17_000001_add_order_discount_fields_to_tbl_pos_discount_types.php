<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderDiscountFieldsToTblPosDiscountTypes extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('tbl_pos_discount_types', 'is_order_discount')) {
            Schema::table('tbl_pos_discount_types', function (Blueprint $table) {
                // Existing discount types continue to apply per product.
                $table->boolean('is_order_discount')->default(0);
            });
        }

        if (!Schema::hasColumn('tbl_pos_discount_types', 'max_discount')) {
            Schema::table('tbl_pos_discount_types', function (Blueprint $table) {
                $table->decimal('max_discount', 15, 3)->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tbl_pos_discount_types', 'max_discount')) {
            Schema::table('tbl_pos_discount_types', function (Blueprint $table) {
                $table->dropColumn('max_discount');
            });
        }

        if (Schema::hasColumn('tbl_pos_discount_types', 'is_order_discount')) {
            Schema::table('tbl_pos_discount_types', function (Blueprint $table) {
                $table->dropColumn('is_order_discount');
            });
        }
    }
}
