<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblInveItemStockOpening extends Model
{
    protected $table = 'tbl_inve_item_stock_opening';
    protected $primaryKey = 'opening_stock_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls() {
        return $this->hasMany(TblInveItemStockOpeningDtl::class, 'opening_stock_id')
            ->with('product','barcode','uom');
    }

}
