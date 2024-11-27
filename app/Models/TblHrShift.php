<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrShift extends Model
{
    protected $table = 'tbl_payr_shift';
    protected $primaryKey = 'shift_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
