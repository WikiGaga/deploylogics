<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrLeaveType extends Model
{
    protected $table = 'tbl_payr_leave_type';
    protected $primaryKey = 'leave_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
