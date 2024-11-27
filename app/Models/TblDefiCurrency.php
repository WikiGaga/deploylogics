<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiCurrency extends Model
{
    protected $table = 'tbl_defi_currency';
    protected $primaryKey = 'currency_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
