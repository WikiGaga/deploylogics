<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiShortcutKeys extends Model
{
    protected $table = 'tbl_defi_shortcut_keys';
    protected $primaryKey = 'shortcut_keys_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
