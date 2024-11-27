<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcChangeRate extends Model
{
    protected $table = 'tbl_purc_change_rate';
    protected $primaryKey = 'change_rate_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    function change_rate_dtl(){
        return $this->hasMany(TblPurcChangeRateDtl::class, 'change_rate_id')
        ->with('product','barcode','uom');
    }
}
