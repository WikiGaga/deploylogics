<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcDemandApproval extends Model
{
    protected $table = 'tbl_purc_demand_approval';
    protected $primaryKey = 'demand_approval_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
