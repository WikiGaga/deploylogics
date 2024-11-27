<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcPoGrnStock extends Model
{
    protected $table = 'vw_purc_po_grn_stock';
    protected $primaryKey = 'purchase_order_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
