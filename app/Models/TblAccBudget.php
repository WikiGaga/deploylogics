<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccBudget extends Model
{
    protected $table = 'tbl_acco_budget';
    protected $primaryKey = 'budget_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function accounts(){
        return $this->belongsTo(TblAccCoa::class, 'chart_account_id' , 'chart_account_id');
    }
}
