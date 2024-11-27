<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcLpoDtl extends Model
{
    protected $table = 'tbl_purc_lpo_dtl';
    protected $primaryKey = 'lpo_dtl_id';

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
        return $this->belongsTo(TblPurcPacking::class, 'lpo_dtl_packing');
    }
    function supplier(){
        return $this->belongsTo(TblPurcSupplier::class, 'supplier_id');
    }
    function branch(){
        return $this->belongsTo(TblSoftBranch::class, 'lpo_dtl_branch_id');
    }
    function sub_dtls(){
        return $this->hasMany(TblPurcLpoDtlDtl::class, 'lpo_dtl_id') ->with('product','barcode','uom','packing','supplier','branch');
    }
}
