<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccoBankRecDtl extends Model
{
    protected $table = 'tbl_acco_bank_rec_dtl';
    protected $primaryKey = 'bank_rec_dtl_id';
    protected $fillable = [
        'bank_rec_dtl_id',
        'bank_rec_id',
        'bank_rec_sr',
        'bank_rec_voucher_id',
        'bank_rec_voucher_date',
        'bank_rec_voucher_no',
        'bank_rec_voucher_descrip',
        'bank_rec_voucher_debit',
        'bank_rec_voucher_credit',
        'bank_rec_voucher_chqno',
        'bank_rec_voucher_chqdate',
        'bank_rec_voucher_posted',
        'bank_rec_voucher_mode_no',
        'bank_rec_voucher_notes',
        'business_id',
        'company_id',
        'branch_id',
        'bank_rec_voucher_cleared_date',
    ];
    protected $guarded = [
        'created_at',
        'updated_at'
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

}
