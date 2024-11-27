<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrGrade extends Model
{
    protected $table = 'tbl_payr_grade';
    protected $primaryKey = 'grade_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
