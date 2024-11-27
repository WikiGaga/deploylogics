<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAutoDemandDtl extends Model
{
    protected $table = 'tbl_purc_auto_demand_dtl';
    protected $primaryKey = 'ad_dtl_id';

    public $timestamps = false;
    
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id')->orderBy('product_name' , 'asc');
    }
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id');
    }
    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'product_unit_id', 'uom_id');
    }
    function packing(){
        return $this->belongsTo(TblPurcPacking::class, 'demand_packing' , 'packing_id');
    }
    function supplier(){
        return $this->belongsTo(TblPurcSupplier::class, 'supplier_id');
    }
    function demand(){
        return $this->belongsTo(TblPurcDemand::class, 'demand_id');
    }
    function branch(){
        return $this->belongsTo(TblSoftBranch::class, 'demand_branch_id');
    }
    // function sub_dtls(){
    //     return $this->hasMany(TblPurcLpoDtlDtl::class, 'lpo_dtl_id') ->with('product','barcode','uom','packing','supplier','branch');
    // }
}
