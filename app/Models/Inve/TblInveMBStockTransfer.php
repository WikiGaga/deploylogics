<?php

namespace App\Models\Inve;

use App\Models\Purc\TblPurcPurchasing;
use Illuminate\Database\Eloquent\Model;

class TblInveMBStockTransfer extends Model
{
    protected $table = 'tbl_inve_mb_stock_transfer';

    protected $primaryKey = 'mb_stock_transfer_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function dtl(){
        return $this->hasMany(TblInveMBStockTransferDtl::class,'mb_stock_transfer_id')
            ->with('transferQty')->orderBy('mb_stock_transfer_dtl_sr');
    }
    public function purchasing(){
        return $this->belongsTo(TblPurcPurchasing::class,'purchasing_id')->select(['purchasing_id','purchasing_code']);
    }
}
