<?php

namespace App\Models\Inve;

use Illuminate\Database\Eloquent\Model;

class TblInveMBStockTransferQty extends Model
{
    protected $table = 'tbl_inve_mb_stock_transfer_qty';

    protected $primaryKey = 'mb_stock_transfer_qty_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
