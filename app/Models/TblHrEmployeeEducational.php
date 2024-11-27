<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrEmployeeEducational extends Model
{
    protected $table = 'tbl_payr_employee_educational';
    protected $primaryKey = 'employee_educational_id';

    public $fillable = ['employee_educational_id','employee_id','employee_educational_sr_no','employee_educational_degree_name','employee_educational_marks','employee_educational_grade','employee_educational_subject_detail','employee_educational_board_name','employee_educational_passing_year'];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
