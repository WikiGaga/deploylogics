<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblPosDiscountTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pos_discount_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->float('default_discount', 8, 2)->default(0);
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_pos_discount_types');
    }
}
