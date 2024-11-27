<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcProductFOC extends Model
{
    protected $table = 'tbl_purc_product_foc';
    protected $primaryKey = 'product_foc_id';
    protected $fillable = ['product_foc_id','sr_no','product_id','supplier_id','product_foc_purc_qty','product_foc_foc_qty','branch_id'];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function supplier(){
        return $this->belongsTo(TblPurcSupplier::class,'supplier_id','supplier_id')->select(['supplier_id','supplier_name']);
    }
}
