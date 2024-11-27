<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiDelTypes extends Model
{
    protected $table = 'tbl_defi_delivery_types';
    protected $primaryKey = 'delivery_type_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
