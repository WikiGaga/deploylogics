<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPayrLeavePolicy extends Model
{
    protected $table = 'tbl_payr_leave_policy';
    protected $primaryKey = 'leave_policy_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function leave_policy_dtls() {
        return $this->hasMany(TblPayrPolicyCriteria::class, 'criteria_document_id','leave_policy_id');
    }
}
