<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewSaleSalesInvoice extends Model
{
    protected $table = 'vw_sale_sales_invoice';
    protected $primaryKey = 'sales_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
