<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccoPaymentMode extends Model
{
    protected $table = 'tbl_acco_payment_mode';
    protected $primaryKey = 'payment_mode_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
