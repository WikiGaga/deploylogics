<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPosDiscountType extends Model
{
    protected $table = 'tbl_pos_discount_types';
    protected $primaryKey = 'id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
