<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcPurchaseOrderListing extends Model
{
    //
    protected $table = 'vw_purc_purchase_order_listing';
    protected $primaryKey = 'purchase_order_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
