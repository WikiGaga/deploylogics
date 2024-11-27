<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleCustomerDtl extends Model
{
    protected $table = 'tbl_sale_customer_dtl';
    protected $primaryKey = 'customer_dtl_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
