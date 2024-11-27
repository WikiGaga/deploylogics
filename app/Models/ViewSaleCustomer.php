<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewSaleCustomer extends Model
{
    protected $table = 'vw_sale_customer';
    protected $primaryKey = 'customer_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
