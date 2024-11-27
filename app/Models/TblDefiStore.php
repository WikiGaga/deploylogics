<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiStore extends Model
{
    protected $table = 'tbl_defi_store';
    protected $primaryKey = 'store_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
