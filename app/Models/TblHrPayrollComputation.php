<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrPayrollComputation extends Model
{
    protected $table = 'tbl_payr_payroll_computation';
    protected $primaryKey = 'payroll_computation_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function payroll_allowance()
    {
        return $this->hasMany(TblHrPayrollCompoutationAllowance::class, 'payroll_computation_id');
    }
    public function payroll_deduction()
    {
        return $this->hasMany(TblHrPayrollDeduction::class, 'payroll_computation_id');
    }
}
