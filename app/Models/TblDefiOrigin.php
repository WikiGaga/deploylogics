<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiOrigin extends Model
{
    protected $table = 'tbl_defi_origin';
    protected $primaryKey = 'origin_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
