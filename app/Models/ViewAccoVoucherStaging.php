<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewAccoVoucherStaging extends Model
{
    protected $table = 'vw_acco_voucher_staging';
    protected $primaryKey = 'voucher_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
