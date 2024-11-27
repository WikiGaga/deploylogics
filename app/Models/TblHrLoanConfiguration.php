<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrLoanConfiguration extends Model
{
    protected $table = 'tbl_payr_loan_configuration';
    protected $primaryKey = 'loan_configuration_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function leave_policy_dtls() {
        return $this->hasMany(TblPayrPolicyCriteria::class, 'criteria_document_id','loan_configuration_id');
    }
}
