<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcSupplier extends Model
{
    protected $table = 'tbl_purc_supplier';
    protected $primaryKey = 'supplier_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function sub_supplier()
    {
        return $this->hasMany(TblPurcSupplierDtl::class,"supplier_id");
    }
    public function supplier_acc()
    {
        return $this->hasMany(TblPurcSupplierAccount::class,"supplier_id");
    }
    public function supplier_branches()
    {
        return $this->hasMany(TblPurcSupplierBranch::class,"supplier_id");
    }
}
