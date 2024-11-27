<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrNationality extends Model
{
    protected $table = 'tbl_payr_nationality';
    protected $primaryKey = 'nationality_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
