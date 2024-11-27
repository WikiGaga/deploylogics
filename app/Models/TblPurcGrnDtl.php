<?php

namespace App\Models;

use App\Models\Defi\TblDefiConstants;
use Illuminate\Database\Eloquent\Model;

class TblPurcGrnDtl extends Model
{
    protected $table = 'tbl_purc_grn_dtl';
    protected $primaryKey = 'purc_grn_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id')
            ->with('uom');
    }
    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }
    function packing(){
        return $this->belongsTo(TblPurcPacking::class, 'tbl_purc_grn_dtl_packing');
    }
    function supplier(){
        return $this->belongsTo(TblPurcSupplier::class, 'supplier_id');
    }
    function constants(){
        return $this->belongsTo(TblDefiConstants::class,'constants_id');
    }

    function purchase_order(){
        return $this->belongsTo(TblPurcPurchaseOrder::class, 'purchase_order_id')
            ->select(['purchase_order_id','purchase_order_code']);
    }

    public function barcode_smpl_data() {
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id')
            ->select('product_barcode_id','product_barcode_barcode');
    }
    public function product_smpl_data() {
        return $this->belongsTo(TblPurcProduct::class, 'product_id')
            ->select('product_id','product_name');
    }
}
