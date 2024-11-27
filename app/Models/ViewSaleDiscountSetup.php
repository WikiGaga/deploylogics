<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewSaleDiscountSetup extends Model
{
    protected $table = 'vw_sale_discount_setup';
    protected $primaryKey = 'discount_setup_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
