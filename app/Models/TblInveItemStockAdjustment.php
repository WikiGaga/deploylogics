<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblInveItemStockAdjustment extends Model
{
    protected $table = 'tbl_inve_item_stock_adjustment';
    protected $primaryKey = 'stock_adjustment_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls() {
        return $this->hasMany(TblInveItemStockAdjustmentDtl::class, 'stock_adjustment_id')
            ->with('product','uom','barcode');
    }

}
