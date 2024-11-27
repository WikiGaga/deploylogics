<?php

namespace App\Models\Settings;

use App\Models\TblAccCoa;
use Illuminate\Database\Eloquent\Model;

class TblDefiExpenseAccounts extends Model
{
    protected $table = 'tbl_defi_expense_accounts';

    protected $primaryKey = 'expense_accounts_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function account() {
        return $this->belongsTo(TblAccCoa::class,"chart_account_id")->select(['chart_account_id','chart_name','chart_code']);
    }
}
