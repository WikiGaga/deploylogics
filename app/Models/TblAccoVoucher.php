<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccoVoucher extends Model
{
    protected $table = 'tbl_acco_voucher';
    protected $primaryKey = 'voucher_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    function accounts(){
        return $this->belongsTo(TblAccCoa::class, 'chart_account_id', 'chart_account_id');
    }
    function bank(){
        return $this->belongsTo(TblDefiBank::class, 'bank_id');
    }
    function payment_mode(){
        return $this->belongsTo(TblAccoPaymentTerm::class,'voucher_payment_mode','payment_term_id');
    }

    function voucher_bill(){
        return $this->hasMany(TblAccoVoucherBillDtl::class, 'voucher_id')->orderBy('voucher_bill_sr_no');
    }

}
