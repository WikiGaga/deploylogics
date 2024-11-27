<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewAccoVoucherListing extends Model
{
    //
    protected $table = 'vw_acco_voucher_listing';
    protected $primaryKey = 'voucher_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
