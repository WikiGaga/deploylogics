<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('e_services', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

//
        $tbl_serv_manage_schedule = 'tbl_serv_manage_schedule';
   if (!Schema::hasTable($tbl_serv_manage_schedule)) {
       Schema::create('tbl_serv_manage_schedule', function (Blueprint $table) {
           $table->bigInteger('schedule_id')->primary();
           $table->timestamps();
       });
   }
   Schema::table($tbl_serv_manage_schedule, function (Blueprint $table) use ($tbl_serv_manage_schedule) {
       if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_id')) {
           $table->text('schedule_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_code')) {
           $table->string('schedule_code')->nullable();
       }
       if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_code_type')) {
           $table->string('schedule_code_type')->nullable();
       }
       if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_date')) {
           $table->date('schedule_date')->nullable();
       }

       if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_start_time')) {
        $table->string('schedule_start_time')->nullable();
    }
    if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_interval_minutes')) {
        $table->bigInteger('schedule_interval_minutes')->nullable();
    }
    if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_assign_to')) {
        $table->bigInteger('schedule_assign_to')->nullable();
    }

    if (!Schema::hasColumn($tbl_serv_manage_schedule,'notes')) {
        $table->string('notes')->nullable();
    }
    if (!Schema::hasColumn($tbl_serv_manage_schedule,'request_quotation_id')) {
        $table->bigInteger('request_quotation_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_serv_manage_schedule,'sales_order_id')) {
        $table->bigInteger('sales_order_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_dtl_schedule_date')) {
        $table->date('schedule_dtl_schedule_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_dtl_schedule_time')) {
        $table->string('schedule_dtl_schedule_time')->nullable();
    }
    if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_status')) {
        $table->bigInteger('schedule_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_uuid')) {
        $table->bigInteger('schedule_uuid')->nullable();
    }
    if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_dtl_id')) {
        $table->bigInteger('schedule_dtl_id')->nullable();
    }
      
       if (!Schema::hasColumn($tbl_serv_manage_schedule,'business_id')) {
           $table->bigInteger('business_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_serv_manage_schedule,'company_id')) {
           $table->bigInteger('company_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_serv_manage_schedule,'branch_id')) {
           $table->bigInteger('branch_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_serv_manage_schedule,'schedule_user_id')) {
           $table->bigInteger('schedule_user_id')->nullable();
       }
   });

//update services tbl_serv_update_status

   $tbl_serv_update_status = 'tbl_serv_update_status';
   if (!Schema::hasTable($tbl_serv_update_status)) {
       Schema::create('tbl_serv_update_status', function (Blueprint $table) {
           $table->bigInteger('update_status_id')->primary();
           $table->timestamps();
       });
   }
   Schema::table($tbl_serv_update_status, function (Blueprint $table) use ($tbl_serv_update_status) {
       if (!Schema::hasColumn($tbl_serv_update_status,'update_status_id')) {
           $table->text('update_status_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_serv_update_status,'quotation_id')) {
           $table->bigInteger('quotation_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_serv_update_status,'order_id')) {
           $table->bigInteger('order_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_serv_update_status,'status_id')) {
           $table->bigInteger('status_id')->nullable();
       }

       if (!Schema::hasColumn($tbl_serv_update_status,'status_date')) {
        $table->date('status_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_serv_update_status,'schedule_id')) {
        $table->bigInteger('schedule_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_serv_update_status,'update_status_code')) {
        $table->string('update_status_code')->nullable();
    }

    if (!Schema::hasColumn($tbl_serv_update_status,'update_status_code_type')) {
        $table->string('update_status_code_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_serv_update_status,'notes')) {
        $table->bigInteger('notes')->nullable();
    }
     
       if (!Schema::hasColumn($tbl_serv_update_status,'business_id')) {
           $table->bigInteger('business_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_serv_update_status,'company_id')) {
           $table->bigInteger('company_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_serv_update_status,'branch_id')) {
           $table->bigInteger('branch_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_serv_update_status,'update_status_user_id')) {
           $table->bigInteger('update_status_user_id')->nullable();
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
       // Schema::dropIfExists('e_services');
    }
}


