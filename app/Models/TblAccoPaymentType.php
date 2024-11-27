<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccoPaymentType extends Model
{
    protected $table = 'tbl_acco_payment_type';
    protected $primaryKey = 'payment_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
