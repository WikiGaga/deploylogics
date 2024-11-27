<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrReligion extends Model
{
    protected $table = 'tbl_payr_religion';
    protected $primaryKey = 'religion_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
