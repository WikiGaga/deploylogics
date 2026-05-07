<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_cheque_layouts', function (Blueprint $table) {
            // Oracle uses VARCHAR2 for string columns
            // We'll make it nullable so existing rows don't cause errors
            $table->string('color_code', 20)->nullable()->after('some_existing_column');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_cheque_layouts', function (Blueprint $table) {
            $table->dropColumn('color_code');
        });
    }
};