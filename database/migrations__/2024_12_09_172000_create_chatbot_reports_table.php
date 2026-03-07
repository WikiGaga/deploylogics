<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatbotReportsTable extends Migration
{
    public function up()
    {
        Schema::create('chatbot_reports', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('conversation_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->index('conversation_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chatbot_reports');
    }
}

