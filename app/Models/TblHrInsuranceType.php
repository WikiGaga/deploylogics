<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrInsuranceType extends Model
{
    protected $table = 'tbl_payr_insurance_type';
    protected $primaryKey = 'insurance_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
