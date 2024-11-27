<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleBarcodePriceTagDtl extends Model
{
    protected $table = 'tbl_sale_barcode_price_tag_dtl';
    protected $primaryKey = 'barcode_price_tag_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }

    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }
}
