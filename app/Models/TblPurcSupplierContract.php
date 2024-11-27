<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcSupplierContract extends Model
{
    protected $table = 'tbl_purc_supplier_contract';
    protected $primaryKey = 'contract_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function contractDtl()
    {
        return $this->hasMany(TblPurcSupplierContractDtl::class,"contract_id")
        ->with('product')
        ->orderBy('contract_dtl_sr_no','asc');
    }
    public function supplier()
    {
        return $this->belongsTo(TblPurcSupplier::class,"supplier_id");
    }
}
