<?php

namespace App\Models;

use App\Models\Settings\TblDefiExpenseAccounts;
use Illuminate\Database\Eloquent\Model;

class TblPurcGrnExpense extends Model
{
    protected $table = 'tbl_purc_grn_expense';
    protected $primaryKey = 'grn_expense_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function accounts(){
        return $this->belongsTo(TblAccCoa::class, 'chart_account_id');
    }
    function exp_acc_dtl(){
        return $this->belongsTo(TblDefiExpenseAccounts::class, 'chart_account_id','chart_account_id')
            ->where('expense_accounts_type','grn_acc');
    }
}
