<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcAutoDemand extends Model
{
    protected $table = 'tbl_purc_auto_demand';
    protected $primaryKey = 'ad_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
