<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcStockDtl extends Model
{
    protected $table = 'vw_purc_stock_dtl';
    protected $primaryKey = 'product_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
