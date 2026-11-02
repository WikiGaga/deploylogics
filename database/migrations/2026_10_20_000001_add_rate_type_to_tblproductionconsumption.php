<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRateTypeToTblproductionconsumption extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tblproductionconsumption')) {
            return;
        }

        Schema::table('tblproductionconsumption', function (Blueprint $table) {
            if (!Schema::hasColumn('tblproductionconsumption', 'rate_type')) {
                $table->string('rate_type', 64)->nullable();
            }
            if (!Schema::hasColumn('tblproductionconsumption', 'rate_perc')) {
                $table->string('rate_perc', 32)->nullable();
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('tblproductionconsumption')) {
            return;
        }

        Schema::table('tblproductionconsumption', function (Blueprint $table) {
            if (Schema::hasColumn('tblproductionconsumption', 'rate_type')) {
                $table->dropColumn('rate_type');
            }
            if (Schema::hasColumn('tblproductionconsumption', 'rate_perc')) {
                $table->dropColumn('rate_perc');
            }
        });
    }
}
