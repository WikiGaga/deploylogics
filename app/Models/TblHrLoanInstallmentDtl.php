<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrLoanInstallmentDtl extends Model
{
    protected $table = 'tbl_payr_loan_installment_dtl';
    protected $primaryKey = 'loan_installment_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
