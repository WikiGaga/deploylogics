<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStagingProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for TBL_STG_ACTIONS
        Schema::create('tbl_stg_actions', function (Blueprint $table) {
            $table->bigIncrements('stg_actions_id');
            $table->string('stg_actions_name')->default('');
            $table->integer('stg_actions_entry_status')->default(1);
            $table->unsignedBigInteger('stg_actions_user_id');
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('branch_id');
            $table->timestamps();
            $table->foreign('stg_actions_user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('business_id')->references('business_id')->on('tbl_soft_business')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('company_id')->references('company_id')->on('tbl_soft_company')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('branch_id')->references('branch_id')->on('tbl_soft_branch')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        // Create table for TBL_STG_FLOWS
        Schema::create('tbl_stg_flows', function (Blueprint $table) {
            $table->bigIncrements('stg_flows_id');
            $table->string('stg_flows_name')->default('');
            $table->integer('stg_flows_entry_status')->default(1);
            $table->unsignedBigInteger('stg_flows_user_id');
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('branch_id');
            $table->timestamps();
            $table->foreign('stg_flows_user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('business_id')->references('business_id')->on('tbl_soft_business')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('company_id')->references('company_id')->on('tbl_soft_company')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('branch_id')->references('branch_id')->on('tbl_soft_branch')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        // Create table for TBL_STG_FORM_CASES
        Schema::create('tbl_stg_form_cases', function (Blueprint $table) {
            $table->bigIncrements('stg_form_cases_id');
            $table->integer('menu_dtl_id')->nullable();
            $table->integer('form_id')->nullable();
            $table->integer('stg_form_cases_entry_status')->default(1);
            $table->unsignedBigInteger('stg_form_cases_user_id');
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('branch_id');
            $table->timestamps();
            $table->foreign('stg_form_cases_user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('business_id')->references('business_id')->on('tbl_soft_business')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('company_id')->references('company_id')->on('tbl_soft_company')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('branch_id')->references('branch_id')->on('tbl_soft_branch')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        // Create table for TBL_STG_FORM_FLOWS // for one to many relation
        Schema::create('tbl_stg_form_flows', function (Blueprint $table) {
            $table->bigIncrements('stg_form_flows_id');
            $table->unsignedBigInteger('stg_form_cases_id');
            $table->unsignedBigInteger('stg_flows_id');
            $table->timestamps();
            $table->foreign('stg_form_cases_id')->references('stg_form_cases_id')->on('tbl_stg_form_cases')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('stg_flows_id')->references('stg_flows_id')->on('tbl_stg_flows')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        // Create table for TBL_STG_FORM_FLOW_PROCESS // for complex poly relation
        Schema::create('tbl_stg_form_flow_process', function (Blueprint $table) {
            $table->unsignedBigInteger('stg_form_cases_id');
            $table->unsignedBigInteger('stg_flows_id');
            $table->unsignedBigInteger('process_id');
            $table->string('process_type');
            $table->foreign('stg_form_cases_id')->references('stg_form_cases_id')->on('tbl_stg_form_cases')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('stg_flows_id')->references('stg_flows_id')->on('tbl_stg_flows')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        // Create table for TBL_STG_FORM_LOG
        Schema::create('tbl_stg_form_log', function (Blueprint $table) {
            $table->bigIncrements('stg_form_log_id');
            $table->unsignedBigInteger('stg_form_cases_id');
            $table->integer('menu_dtl_id');
            $table->integer('form_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('stg_flows_id');
            $table->unsignedBigInteger('stg_actions_id');
            $table->integer('stg_form_log_entry_status')->default(1);
            $table->unsignedBigInteger('stg_form_log_user_id');
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('branch_id');
            $table->timestamps();

            $table->foreign('stg_form_cases_id')->references('stg_form_cases_id')->on('tbl_stg_form_cases')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('stg_form_log_user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('stg_flows_id')->references('stg_flows_id')->on('tbl_stg_flows')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('stg_actions_id')->references('stg_actions_id')->on('tbl_stg_actions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('business_id')->references('business_id')->on('tbl_soft_business')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('company_id')->references('company_id')->on('tbl_soft_company')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('branch_id')->references('branch_id')->on('tbl_soft_branch')
                ->onUpdate('cascade')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_stg_actions');
        Schema::dropIfExists('tbl_stg_flows');
        Schema::dropIfExists('tbl_stg_form_cases');
        Schema::dropIfExists('tbl_stg_form_flows');
        Schema::dropIfExists('tbl_stg_form_flow_process');
        Schema::dropIfExists('tbl_stg_form_log');
    }
}
