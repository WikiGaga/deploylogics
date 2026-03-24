<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrRoasterShift extends Model
{
    protected $table = 'tbl_hr_roaster_shift';
    protected $primaryKey = 'roaster_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
