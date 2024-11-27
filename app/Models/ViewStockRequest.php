<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewStockRequest extends Model
{
    //
    protected $table = 'vw_stock_request';
    protected $primaryKey = 'demand_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function getDemandDateAttribute($value)
    {
        return date('Y-m-d', strtotime($value));
    }
}
