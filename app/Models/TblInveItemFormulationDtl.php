<?php

namespace App\Models;

use App\Models\Defi\TblDefiConstants;
use Illuminate\Database\Eloquent\Model;

class TblInveItemFormulationDtl extends Model
{
    protected $table = 'tbl_inve_item_formulation_dtl ';
    protected $primaryKey = 'item_formulation_dtl_id';

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
    function constants(){
        return $this->belongsTo(TblDefiConstants::class,'constants_id');
    }
}
