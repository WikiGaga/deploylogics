<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblStgFormFlows extends Model
{
    protected $table = 'tbl_stg_form_flows';
    protected $primaryKey = 'stg_form_flows_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtl() {
        return $this->hasOne(TblStgFlows::class,'stg_flows_id','stg_flows_id');
    }
}
