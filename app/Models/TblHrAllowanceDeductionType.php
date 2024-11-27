<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrAllowanceDeductionType extends Model
{
    protected $table = 'tbl_payr_allowance_deduction_type';
    protected $primaryKey = 'allowance_deduction_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
