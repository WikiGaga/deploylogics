<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrEmployeeExperience extends Model
{
    protected $table = 'tbl_payr_employee_experience';
    protected $primaryKey = 'employee_experience_id';

    public $fillable = ['employee_experience_id','employee_id','employee_experience_sr_no','company_name','field_name','experience_in_year','business_id','company_id','branch_id'];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
