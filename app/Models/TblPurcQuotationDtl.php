<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcQuotationDtl extends Model
{
    protected $table = 'tbl_purc_quotation_dtl';
    protected $primaryKey = 'quotation_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'prod_id');
    }
    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }
    function packing(){
        return $this->belongsTo(TblPurcPacking::class, 'quotation_dtl_packing');
    }

}
