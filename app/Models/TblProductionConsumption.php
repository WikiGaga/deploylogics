<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblProductionConsumption extends Model
{
    protected $table = 'tblproductionconsumption';
    protected $primaryKey = 'code';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function product(){
        return $this->belongsTo(TblPurcProduct::class, 'item_code');
    }
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'item_code');
    }
    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }

}
