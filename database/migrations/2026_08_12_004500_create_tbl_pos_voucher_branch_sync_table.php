<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblPosVoucherBranchSyncTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_pos_voucher_branch_sync', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branch_id')->unique();
            $table->timestamp('last_order_updated_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->integer('last_processed_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_pos_voucher_branch_sync');
    }
}
