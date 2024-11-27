<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrRetirementType extends Model
{
    protected $table = 'tbl_payr_retirement_type';
    protected $primaryKey = 'retirement_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
