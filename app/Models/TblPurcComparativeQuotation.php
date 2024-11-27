<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcComparativeQuotation extends Model
{
    protected $table = 'tbl_purc_comparative_quotation';
    protected $primaryKey = 'comparative_quotation_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
  
    function quotation(){
        return $this->belongsTo(TblPurcQuotation::class, 'quotation_id');
    }
    function dtls(){
        return $this->hasMany(TblPurcComparativeQuotationDtl::class, 'comparative_quotation_id')->with('product','uom','packing' , 'supplier');
    }
    function accounts(){
        return $this->hasMany(TblPurcComparativeQuotationAccount::class, 'comparative_quotation_id');
    }

}