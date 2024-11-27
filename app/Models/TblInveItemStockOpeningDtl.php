<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblInveItemStockOpeningDtl extends Model
{
    protected $table = 'tbl_inve_item_stock_opening_dtl ';
    protected $primaryKey = 'opening_stock_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id');
    }
    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }
}
