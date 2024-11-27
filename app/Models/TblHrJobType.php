<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrJobType extends Model
{
    protected $table = 'tbl_payr_job_type';
    protected $primaryKey = 'job_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
