<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccoPaymentTerm extends Model
{
    protected $table = 'tbl_acco_payment_term';
    protected $primaryKey = 'payment_term_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
