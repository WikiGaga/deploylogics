<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleConsumerProtectionDtl extends Model
{
    protected $table = 'tbl_sale_consumer_protection_dtl ';
    protected $primaryKey = 'protection_dtl_id';

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
    function packing(){
        return $this->belongsTo(TblPurcPacking::class, 'packing_id');
    }
}
