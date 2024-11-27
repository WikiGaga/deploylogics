<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccoVoucherBillDtl extends Model
{
    protected $table = 'tbl_acco_voucher_bill_dtl';
    protected $primaryKey = 'voucher_bill_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }


}
