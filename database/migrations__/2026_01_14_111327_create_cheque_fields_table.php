<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequeFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('TBL_cheque_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layout_id')->constrained('cheque_layouts')->onDelete('cascade');
            $table->string('field_name'); // date, account_title, amount, amount_words
            $table->integer('top_px')->default(0);
            $table->integer('left_px')->default(0);
            $table->integer('width_px')->default(100);
            $table->integer('height_px')->default(30);
            $table->integer('font_size')->default(14);
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
        Schema::dropIfExists('TBL_cheque_fields');
    }
}
