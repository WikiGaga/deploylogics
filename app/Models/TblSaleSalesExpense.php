<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleSalesExpense extends Model
{
    protected $table = 'tbl_sale_sales_expense';
    protected $primaryKey = 'sales_expense_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function accounts(){
        return $this->belongsTo(TblAccCoa::class, 'chart_account_id');
    }
}
