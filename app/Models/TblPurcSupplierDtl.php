<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcSupplierDtl extends Model
{
    protected $table = 'tbl_purc_supplier_sub';
    protected $primaryKey = 'supplier_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
