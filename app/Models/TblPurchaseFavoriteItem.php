<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurchaseFavoriteItem extends Model
{
    protected $table = 'tbl_purchase_favorite_items';
    protected $primaryKey = 'favorite_item_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function favorite()
    {
        return $this->belongsTo(TblPurchaseFavorite::class, 'favorite_id');
    }

    public function product()
    {
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }

    public function barcode()
    {
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id');
    }
}

