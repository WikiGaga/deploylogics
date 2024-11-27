<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrPayrollCompoutationAllowance extends Model
{
    protected $table = 'tbl_payr_payroll_computation_allowance';
    protected $primaryKey = 'allowance_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
}
}