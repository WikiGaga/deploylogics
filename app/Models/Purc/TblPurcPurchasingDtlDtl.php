<?php

namespace App\Models\Purc;

use App\Models\TblDefiUom;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use Illuminate\Database\Eloquent\Model;

class TblPurcPurchasingDtlDtl extends Model
{
    protected $table = 'tbl_purc_purchasing_dtl_dtl';

    protected $primaryKey = 'purchasing_dtl_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id')
            ->select(['product_id','product_name']);
    }
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id')
            ->select(['product_barcode_id','product_barcode_barcode']);
    }
    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'uom_id')
            ->select(['uom_id','uom_name']);
    }
}
