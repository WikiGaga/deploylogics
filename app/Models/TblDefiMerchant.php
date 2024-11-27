<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiMerchant extends Model
{
    protected $table = 'tbl_defi_merchant';
    protected $primaryKey = 'merchant_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
