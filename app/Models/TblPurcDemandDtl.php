<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcDemandDtl extends Model
{
    protected $table = 'tbl_purc_demand_dtl';
    protected $primaryKey = 'demand_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function dtl_product(){
        return $this->hasMany(TblPurcProduct::class,'product_id','product_id');
    }
    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id');
    }
    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'demand_dtl_uom');
    }
    function packing(){
        return $this->belongsTo(TblPurcPacking::class, 'demand_dtl_packing');
    }
    function branch(){
        return $this->belongsTo(TblSoftBranch::class, 'branch_id');
    }
}
