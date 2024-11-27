<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiOrderStatus extends Model
{
    protected $table = 'tbl_defi_order_status';
    protected $primaryKey = 'order_status_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
