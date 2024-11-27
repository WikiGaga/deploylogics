<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcQuotation extends Model
{
    protected $table = 'tbl_purc_quotation';
    protected $primaryKey = 'quotation_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    function supplier(){
        return $this->belongsTo(TblPurcSupplier::class, 'quotation_supplier_id');
    }
    function lpo(){
        return $this->belongsTo(TblPurcLpo::class, 'lpo_id');
    }
    function dtls(){
        return $this->hasMany(TblPurcQuotationDtl::class, 'quotation_id')->with('product','uom','packing');
    }
    function accounts(){
        return $this->hasMany(TblPurcQuotationAccount::class, 'quotation_id');
    }

}
