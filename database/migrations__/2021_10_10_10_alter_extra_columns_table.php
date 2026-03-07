<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterExtraColumnsTable extends Migration
{
    /**
     * Run the migrations.
     * php artisan migrate:refresh --path=/database/migrations/2021_10_10_10_alter_extra_columns_table.php
     * php artisan migrate:refresh --path=/database/migrations/2021_10_10_10_vw_acco_voucher.php
     * @return void
     */
    public function up()
    {
        $tbl_sale_bank_distribution = 'tbl_sale_bank_distribution';
        Schema::table($tbl_sale_bank_distribution, function (Blueprint $table) use ($tbl_sale_bank_distribution) {
            if (!Schema::hasColumn($tbl_sale_bank_distribution,'document_verified_status')) {
                $table->bigInteger('document_verified_status')->nullable();
            }
        });
        $tbl_acco_voucher = 'tbl_acco_voucher';
        Schema::table($tbl_acco_voucher, function (Blueprint $table) use ($tbl_acco_voucher) {
            if (!Schema::hasColumn($tbl_acco_voucher,'bank_rec_cheque_status_id')) {
                $table->bigInteger('bank_rec_cheque_status_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_acco_voucher,'bank_rec_voucher_notes')) {
                $table->string('bank_rec_voucher_notes')->nullable();
            }

        });
        $tbl_defi_document_upload = 'tbl_defi_document_upload';
        Schema::table($tbl_defi_document_upload, function (Blueprint $table) use ($tbl_defi_document_upload) {
            if (!Schema::hasColumn($tbl_defi_document_upload,'menu_id')) {
                $table->bigInteger('menu_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_defi_document_upload,'document_refrence_number')) {
                $table->string('document_refrence_number')->nullable();
            }
            if (!Schema::hasColumn($tbl_defi_document_upload,'document_place_of_issue')) {
                $table->string('document_place_of_issue')->nullable();
            }
            if (!Schema::hasColumn($tbl_defi_document_upload,'document_date_of_issue')) {
                $table->date('document_date_of_issue')->nullable();
            }
            if (!Schema::hasColumn($tbl_defi_document_upload,'document_date_of_expiry')) {
                $table->date('document_date_of_expiry')->nullable();
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
