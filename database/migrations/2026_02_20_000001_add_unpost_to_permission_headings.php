<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddUnpostToPermissionHeadings extends Migration
{
    public function up()
    {
        $exists = DB::table('permission_headings')
            ->where('name', 'un_post_module')
            ->exists();

        if (!$exists) {
            $nextId = DB::table('permission_headings')->max('id') + 1;
            DB::table('permission_headings')->insert([
                'id' => $nextId,
                'name' => 'un_post_module',
                'display_name' => 'Unpost',
                'icon' => 'fa fa-undo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        DB::table('permission_headings')->where('name', 'un_post_module')->delete();
    }
}
