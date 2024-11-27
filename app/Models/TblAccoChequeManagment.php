<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccoChequeManagment extends Model
{
    protected $table = 'tbl_acco_cheque_managment';
    protected $primaryKey = 'cheque_managment_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function dtls() {
        return $this->hasMany(TblAccoChequeManagmentDtl::class, 'cheque_managment_id')->with('accounts');
    }
}
