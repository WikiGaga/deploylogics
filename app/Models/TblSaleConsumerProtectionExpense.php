<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleConsumerProtectionExpense extends Model
{
    protected $table = 'tbl_sale_consumer_protection_expense';
    protected $primaryKey = 'protection_expense_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function accounts(){
        return $this->belongsTo(TblAccCoa::class, 'chart_account_id');
    }
}
