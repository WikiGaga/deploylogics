<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrEmployeeInsurance extends Model
{
    protected $table = 'tbl_payr_employee_insurance';
    protected $primaryKey = 'employee_insurance_id';

    public $fillable = ['employee_insurance_id','employee_id','employee_insurance_sr_no','insurance_company_id','employee_insurance_health_name','employee_insurance_rate_for_foreign','employee_insurance_rate_settlement','insurance_type_id','employee_insurance_start_date','employee_insurance_end_date','business_id','company_id','branch_id'];


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
