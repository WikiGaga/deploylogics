<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftDiscountTypes extends Model
{
    protected $table = 'tbl_soft_discount_types';
    protected $primaryKey = 'id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
