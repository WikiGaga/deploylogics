<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrDesignation extends Model
{
    protected $table = 'tbl_payr_designation';
    protected $primaryKey = 'designation_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
