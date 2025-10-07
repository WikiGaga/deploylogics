<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneratedReportsTable extends Migration
{
    public function up()
    {
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description');
            $table->longText('query');
            $table->string('type')->default('custom');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('created_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('generated_reports');
    }
}
