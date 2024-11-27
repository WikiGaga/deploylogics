<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrVisaType extends Model
{
    protected $table = 'tbl_payr_visa_types';
    protected $primaryKey = 'visa_types_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
