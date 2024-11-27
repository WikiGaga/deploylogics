<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrLoan extends Model
{
    protected $table = 'tbl_payr_loan';
    protected $primaryKey = 'loan_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function loan_installment_dtl() {
        return $this->hasMany(TblHrLoanInstallmentDtl::class,'loan_id');
    }
}
