<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcDemandApproval extends Model
{
    protected $table = 'vw_purc_demand_approval';
    protected $primaryKey = 'demand_approval_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function product_dtl(){
        return $this->belongsTo(TblPurcProduct::class , 'product_id')->with('product_barcode','supplier');
    }
}
