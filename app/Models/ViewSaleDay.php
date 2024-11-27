<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewSaleDay extends Model
{
    protected $table = 'vw_sale_day';
    protected $primaryKey = 'day_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
