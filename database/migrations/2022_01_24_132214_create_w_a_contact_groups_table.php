<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWAContactGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wa_contact_group', function (Blueprint $table) {
            $table->increments('grp_id')->primary();
            $table->string('grp_name');
            $table->char('is_active' , 10);
            $table->integer('company_id');
            $table->integer('business_id');
            $table->integer('branch_id');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wa_contact_group');
    }
}
