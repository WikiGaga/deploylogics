<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleCustomerType extends Model
{
    protected $table = 'tbl_sale_customer_type';
    protected $primaryKey = 'customer_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
