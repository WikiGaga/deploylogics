<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrInsuranceCompany extends Model
{
    protected $table = 'tbl_payr_insurance_company';
    protected $primaryKey = 'insurance_company_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
