<?php

namespace App\Models\Purc;

use App\Models\Purc\TblPurcPurchasingDtl;
use App\Models\TblSoftBranch;
use Illuminate\Database\Eloquent\Model;

class TblPurcPurchasing extends Model
{
    protected $table = 'tbl_purc_purchasing';

    protected $primaryKey = 'purchasing_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtl() {
        return $this->hasMany(TblPurcPurchasingDtl::class,'purchasing_id')
            ->with('dtl_dtl','product','barcode','uom','branch')
            ->orderBy('purchasing_dtl_sr_no');
    }
}
