<?php

namespace App\Models\Purc;

use App\Models\TblDefiUom;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblSoftBranch;
use Illuminate\Database\Eloquent\Model;

class TblPurcPurchasingDtl extends Model
{
    protected $table = 'tbl_purc_purchasing_dtl';

    protected $primaryKey = 'purchasing_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function dtl_dtl() {
        return $this->hasMany(TblPurcPurchasingDtlDtl::class,'purchasing_dtl_id','purchasing_dtl_no')
            ->with('product','barcode','uom')->orderBy('purchasing_dtl_dtl_sr_no');
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
    public function branch(){
        return $this->hasMany(TblSoftBranch::class,'branch_id','purchasing_dtl_branch_id')
            ->select(['branch_id','branch_name','branch_short_name']);
    }
}
