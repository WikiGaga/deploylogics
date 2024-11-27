<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewAccoChartAccountHelp extends Model
{
    protected $table = 'vw_acco_chart_account_help';
    protected $primaryKey = 'chart_account_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
