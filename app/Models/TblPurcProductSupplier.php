<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcProductSupplier extends Model
{
    protected $table = 'tbl_purc_product_suppliers';
    protected $primaryKey = 'id';

    protected $fillable = ['id','product_id','supplier_id','branch_id','created_by','updated_by'];

    public function product()
    {
        return $this->belongsTo(TblPurcProduct::class, 'product_id', 'product_id');
    }

    public function supplier()
    {
        return $this->belongsTo(TblPurcSupplier::class, 'supplier_id', 'supplier_id');
    }
}
