<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleSalesOrderExpense extends Model
{
    protected $table = 'tbl_sale_sales_order_expense';
    protected $primaryKey = 'sales_order_expense_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function accounts(){
        return $this->belongsTo(TblAccCoa::class, 'chart_account_id');
    }
}
