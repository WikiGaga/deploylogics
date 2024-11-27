<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcSupplierBranch extends Model
{
    protected $table = 'tbl_purc_supplier_branch';
    protected $primaryKey = 'supplier_branch_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
