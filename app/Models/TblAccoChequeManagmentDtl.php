<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccoChequeManagmentDtl extends Model
{
    protected $table = 'tbl_acco_cheque_managment_dtl';
    protected $primaryKey = 'cheque_managment_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function accounts() {
        return $this->belongsTo(TblAccCoa::class, 'chart_account_id');
    }
}
