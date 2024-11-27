<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrQualification extends Model
{
    protected $table = 'tbl_payr_qualification';
    protected $primaryKey = 'qualification_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
