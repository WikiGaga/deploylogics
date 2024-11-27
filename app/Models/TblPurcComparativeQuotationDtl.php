<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcComparativeQuotationDtl extends Model
{
    protected $table = 'tbl_purc_comparative_quotation_dtl';
    protected $primaryKey = 'comparative_quotation_dtl_id';

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
        return $this->belongsTo(TblPurcPacking::class, 'comparative_quotation_dtl_packing');
    }
    function supplier(){
        return $this->belongsTo(TblPurcSupplier::class, 'supplier_id');
    }

}
