<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrGender extends Model
{
    protected $table = 'tbl_payr_gender';
    protected $primaryKey = 'gender_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
