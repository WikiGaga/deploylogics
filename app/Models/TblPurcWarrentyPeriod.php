<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcWarrentyPeriod extends Model
{
    protected $table = 'tbl_purc_warrenty_period';
    protected $primaryKey = 'warrenty_period_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
