<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrAllowance extends Model
{
    protected $table = 'tbl_hr_allowance';
    protected $primaryKey = 'id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
