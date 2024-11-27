<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcDemandApprovalDtl extends Model
{
    protected $table = 'tbl_purc_demand_approval_dtl';
    protected $primaryKey = 'demand_approval_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }
    function branch(){
        return $this->belongsTo(TblSoftBranch::class, 'branch_id');
    }
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id');
    }
    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }
    function demand(){
        return $this->belongsTo(TblPurcDemand::class , 'demand_id');
    }
}
