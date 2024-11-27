<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrCheckList extends Model
{
    protected $table = 'tbl_payr_check_list';
    protected $primaryKey = 'check_list_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
