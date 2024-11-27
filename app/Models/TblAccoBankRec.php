<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccoBankRec extends Model
{
    protected $table = 'tbl_acco_bank_rec';
    protected $primaryKey = 'bank_rec_id';
    protected $fillable = [
        'bank_rec_id',
        'bank_rec_date',
        'bank_rec_sr',
        'bank_rec_bank_id',
        'bank_rec_opening_balance',
        'bank_rec_bank_balance',
        'bank_rec_closing_balance',
        'bank_rec_uncleared_balance',
        'bank_rec_satement_date',
        'bank_rec_start_date',
        'bank_rec_end_date',
        'business_id',
        'company_id',
        'branch_id',
        'bank_rec_user_id',
        'bank_rec_entry_status',
        'bank_rec_notes',
        'bank_rec_code',
        'bank_rec_reconciled'
    ];
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function dtl() {
        return $this->hasMany(TblAccoBankRecDtl::class,'bank_rec_id')->orderBy('bank_rec_sr') ;
    }
    public function bank_acco() {
        return $this->belongsTo(TblAccCoa::class,'bank_rec_bank_id','chart_account_id');
    }
}
