<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrEmployeeType extends Model
{
    protected $table = 'tbl_payr_employee_type';
    protected $primaryKey = 'employee_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
