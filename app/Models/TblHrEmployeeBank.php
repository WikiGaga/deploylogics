<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrEmployeeBank extends Model
{
    protected $table = 'tbl_payr_employee_bank';
    protected $primaryKey = 'employee_bank_id';

    public $fillable = ['employee_bank_id','employee_id','employee_bank_sr_no','chart_bank_id','account_title','account_no','business_id','company_id','branch_id'];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
