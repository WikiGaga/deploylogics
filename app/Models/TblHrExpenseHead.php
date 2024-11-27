<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrExpenseHead extends Model
{
    protected $table = 'tbl_payr_expense_head';
    protected $primaryKey = 'expense_head_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
