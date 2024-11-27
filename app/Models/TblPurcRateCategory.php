<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcRateCategory extends Model
{
    protected $table = 'tbl_purc_rate_category';
    protected $primaryKey = 'rate_category_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
