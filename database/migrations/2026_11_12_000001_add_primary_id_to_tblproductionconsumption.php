<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddPrimaryIdToTblproductionconsumption extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tblproductionconsumption')) {
            return;
        }

        if (!Schema::hasColumn('tblproductionconsumption', 'id')) {
            Schema::table('tblproductionconsumption', function (Blueprint $table) {
                $table->string('id', 36)->nullable();
            });
        }

        DB::table('tblproductionconsumption')
            ->whereNull('id')
            ->select(['code', 'branch_id', 'sr_no'])
            ->orderBy('code')
            ->get()
            ->each(function ($row) {
                $query = DB::table('tblproductionconsumption')
                    ->where('code', $row->code)
                    ->where('sr_no', $row->sr_no);
                $row->branch_id === null
                    ? $query->whereNull('branch_id')
                    : $query->where('branch_id', $row->branch_id);
                $query->whereNull('id')->update(['id' => (string) Str::uuid()]);
            });

        $hasPrimaryKey = DB::selectOne("SELECT COUNT(*) AS CONSTRAINT_COUNT FROM USER_CONSTRAINTS WHERE TABLE_NAME = 'TBLPRODUCTIONCONSUMPTION' AND CONSTRAINT_TYPE = 'P'");
        if ((int) ($hasPrimaryKey->constraint_count ?? $hasPrimaryKey->CONSTRAINT_COUNT ?? 0) === 0) {
            Schema::table('tblproductionconsumption', function (Blueprint $table) {
                $table->primary('id', 'tbl_prod_cons_pk');
            });
        }
    }

    public function down(): void
    {
        // Keep the ID so rollback cannot orphan document references.
    }
};
