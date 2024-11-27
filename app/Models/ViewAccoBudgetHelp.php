<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewAccoBudgetHelp extends Model
{
    protected $table = 'vw_acco_budget_help';
    protected $primaryKey = 'budget_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
