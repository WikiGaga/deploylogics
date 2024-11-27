<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccoChequeBookDtl extends Model
{
    protected $table = 'tbl_acco_cheque_book_dtl';
    protected $primaryKey = 'cheque_book_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
  
}
