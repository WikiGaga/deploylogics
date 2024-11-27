<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcSupProd extends Model
{
    protected $table = 'tbl_purc_sup_prod';
    protected $primaryKey = 'sup_prod_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function sub_prod()
    {
        return $this->hasMany(TblPurcSupProdDtl::class,"sup_prod_id")
        ->with('product','barcode','uom','packing')
        ->orderBy('sup_prod_dtl_sr_no','asc');
    }

    function supplier(){
        return $this->belongsTo(TblPurcSupplier::class, 'supplier_id');
    }
}
