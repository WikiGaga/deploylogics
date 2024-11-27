<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblInveDealDtl extends Model
{
    protected $table = 'tbl_inve_deal_dtl';
    protected $primaryKey = 'stock_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id')
            ->with('purc_rate_first');
    }
    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }
}
