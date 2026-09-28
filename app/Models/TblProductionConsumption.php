<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblProductionConsumption extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'tblproductionconsumption';
    protected $primaryKey = 'code';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function product(){
        return $this->belongsTo(TblPurcProduct::class, 'item_code', 'product_barcode_id');
    }

    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'item_code', 'product_barcode_barcode');
    }

    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }
}
