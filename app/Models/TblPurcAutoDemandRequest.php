<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcAutoDemandRequest extends Model
{
    protected $table = 'tbl_purc_auto_demand_request';
    protected $primaryKey = 'ad_req_id';

    public $timestamps = false;

    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
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
}
