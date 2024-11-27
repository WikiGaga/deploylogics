<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblInveItemStockTransfer extends Model
{
    protected $table = 'tbl_inve_item_stock_transfer';
    protected $primaryKey = 'item_stock_transfer_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls() {
        return $this->hasMany(TblInveItemStockTransferDtl::class, 'item_stock_transfer_id')
            ->with('product','uom','barcode');
    }

}
