<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcSupplierAccount extends Model
{
    protected $table = 'tbl_purc_supplier_account';
    protected $primaryKey = 'supplier_account_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
