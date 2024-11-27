<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcLpo extends Model
{
    protected $table = 'tbl_purc_lpo';
    protected $primaryKey = 'lpo_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls(){
        return $this->hasMany(TblPurcLpoDtl::class,'lpo_id')
            ->with('product','barcode','uom','packing','supplier','sub_dtls','branch')
            ->orderBy('sr_no' , 'asc');
    }
}
