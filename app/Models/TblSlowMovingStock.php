<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSlowMovingStock extends Model
{
    protected $table = 'tbl_slow_moving_items';
    protected $primaryKey = 'slow_moving_item_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
