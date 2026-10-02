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

    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'item_code', 'product_barcode_barcode');
    }
}
