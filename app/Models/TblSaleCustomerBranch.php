<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleCustomerBranch extends Model
{
    protected $table = 'tbl_sale_customer_branch';
    protected $primaryKey = 'customer_branch_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
