<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcSupplierType extends Model
{
    protected $table = 'tbl_purc_supplier_type';
    protected $primaryKey = 'supplier_type_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
