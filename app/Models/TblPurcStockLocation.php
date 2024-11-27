<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcStockLocation extends Model
{
    protected $table = 'tbl_purc_stock_location';
    protected $primaryKey = 'stock_location_id';
    
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
