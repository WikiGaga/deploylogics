<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrAdvanceType extends Model
{
    protected $table = 'tbl_payr_advance_type';
    protected $primaryKey = 'advance_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
