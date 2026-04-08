<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblHrEmployeeComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_hr_employee_components', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2); // DECIMAL(10,2)
            $table->integer('employee_id')->default(null); 
            $table->integer('component_id')->default(null);
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('tbl_hr_employee_components');
    }
}
