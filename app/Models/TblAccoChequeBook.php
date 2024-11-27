<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccoChequeBook extends Model
{
    protected $table = 'tbl_acco_cheque_book';
    protected $primaryKey = 'cheque_book_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function dtls() {
        return $this->hasMany(TblAccoChequeBookDtl::class, 'cheque_book_id');
    }
  
}
