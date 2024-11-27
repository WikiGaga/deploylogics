<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewInveStock extends Model
{
    protected $table = 'vw_inve_stock';
    protected $primaryKey = 'stock_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
