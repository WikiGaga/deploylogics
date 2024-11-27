<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblStgFlows extends Model
{
    protected $table = 'tbl_stg_flows';
    protected $primaryKey = 'stg_flows_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
