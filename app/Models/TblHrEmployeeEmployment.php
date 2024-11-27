<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrEmployeeEmployment extends Model
{
    protected $table = 'tbl_payr_employee_employment';
    protected $primaryKey = 'employee_employment_id';

    public $fillable = ['employee_employment_id','employee_id','employee_employment_sr_no','employee_date','grade_id','employee_type_id','designation_id','department_id','business_id','company_id','branch_id'];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
