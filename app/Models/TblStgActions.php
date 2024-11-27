<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblStgActions extends Model
{
    protected $table = 'tbl_stg_actions';
    protected $primaryKey = 'stg_actions_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

}
