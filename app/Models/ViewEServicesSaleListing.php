<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewEServicesSaleListing extends Model
{
    protected $table = 'vw_sales_listing';
    protected $primaryKey = 'sales_order_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
