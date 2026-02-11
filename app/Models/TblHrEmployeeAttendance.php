<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrEmployeeAttendance extends Model
{
    protected $table = 'tbl_hr_attendence';
    protected $primaryKey = 'id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
