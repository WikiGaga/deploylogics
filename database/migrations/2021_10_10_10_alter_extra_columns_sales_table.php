<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterExtraColumnsSalesTable extends Migration
{
    /**
     * Run the migrations.
     * php artisan migrate:refresh --path=/database/migrations/2021_10_10_10_alter_extra_columns_table.php
     * php artisan migrate:refresh --path=/database/migrations/2021_10_10_10_vw_acco_voucher.php
     * @return void
     */
    public function up()
    {
        $tbl_sale_sales_order = 'tbl_sale_sales_order';
        Schema::table($tbl_sale_sales_order, function (Blueprint $table) use ($tbl_sale_sales_order) {
            if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_booking_id')) {
                $table->bigInteger('sales_order_booking_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_mobile_no')) {
                $table->string('sales_order_mobile_no')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_sales_order,'bank_id')) {
                $table->bigInteger('bank_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_sales_order,'sales_contract_id')) {
                $table->bigInteger('sales_contract_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_rate_type')) {
                $table->string('sales_order_rate_type')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_rate_perc')) {
                $table->decimal('sales_order_rate_perc',15,3)->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_sales_order,'store_id')) {
                $table->bigInteger('store_id')->nullable();
            }
        });
        $tbl_sale_sales_dtl = 'tbl_sale_sales_dtl';
        Schema::table($tbl_sale_sales_dtl, function (Blueprint $table) use ($tbl_sale_sales_dtl) {
            if (!Schema::hasColumn($tbl_sale_sales_dtl,'item_description')) {
                $table->string('item_description')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_sales_dtl,'area_of_display')) {
                $table->string('area_of_display')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_sales_dtl,'display_rent_fee_month')) {
                $table->string('display_rent_fee_month')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_contract_person')) {
                $table->string('sales_contract_person')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_sales_dtl,'purc_amount')) {
                $table->decimal('purc_amount',15,3)->nullable();
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

    }
}
