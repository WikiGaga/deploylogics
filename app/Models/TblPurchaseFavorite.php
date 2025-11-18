<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurchaseFavorite extends Model
{
    protected $table = 'tbl_purchase_favorites';
    protected $primaryKey = 'favorite_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function items()
    {
        return $this->hasMany(TblPurchaseFavoriteItem::class, 'favorite_id')
            ->orderBy('sr_no', 'asc');
    }
}

