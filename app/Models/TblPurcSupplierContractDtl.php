<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcSupplierContractDtl extends Model
{
    protected $table = 'tbl_purc_supplier_contract_dtl';
    protected $primaryKey = 'contract_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }
    function brand(){
        return $this->belongsTo(TblPurcBrand::class, 'contract_dtl_brand');
    }
    function group(){
        return $this->belongsTo(ViewPurcGroupItem::class, 'contract_dtl_group');
    }
}
