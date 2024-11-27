<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewAccoVoucher extends Model
{
    protected $table = 'vw_acco_voucher';
    protected $primaryKey = 'voucher_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
