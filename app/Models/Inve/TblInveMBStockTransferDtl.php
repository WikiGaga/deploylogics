<?php

namespace App\Models\Inve;

use App\Models\TblDefiUom;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use Illuminate\Database\Eloquent\Model;

class TblInveMBStockTransferDtl extends Model
{
    protected $table = 'tbl_inve_mb_stock_transfer_dtl';

    protected $primaryKey = 'mb_stock_transfer_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function transfer_qty(){
        return $this->hasMany(TblInveMBStockTransferQty::class,'mb_stock_transfer_dtl_id')
            ->orderBy('mb_stock_transfer_qty_sr');
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
