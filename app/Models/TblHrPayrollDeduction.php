<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrPayrollDeduction extends Model
{
    protected $table = 'tbl_payr_payroll_computation_deduction';
    protected $primaryKey = 'deduction_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
}
}
